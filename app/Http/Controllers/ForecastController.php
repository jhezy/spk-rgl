<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\Permintaan;
use App\Models\Produksi;
use App\Models\Peramalan;
use App\Services\MultipleLinearRegressionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ForecastController extends Controller
{
    protected $mlr;

    public function __construct(MultipleLinearRegressionService $mlr)
    {
        $this->mlr = $mlr;
    }

    /**
     * Show forecast form
     */
    public function index()
    {
        return view('forecast.index');
    }

    public function exportPdf()
    {
        $peramalan = Peramalan::orderBy('id_peramalan', 'desc')->first();

        if (!$peramalan) {
            return back()->withErrors(['data' => 'Belum ada hasil forecasting yang bisa diexport']);
        }

        // ambil ulang seluruh data historis sama seperti proses forecast (untuk detail table)
        $produksis = Produksi::orderBy('bulan')->get();

        $data = [];
        foreach ($produksis as $p) {
            $bulan = \Carbon\Carbon::parse($p->bulan)->format('Y-m-d');
            $pen  = Penjualan::where('bulan', $bulan)->first();
            $perm = Permintaan::where('bulan', $bulan)->first();

            if ($pen && $perm) {
                $data[] = [
                    'bulan' => $bulan,
                    'x1' => (float)$pen->jumlah_penjualan,
                    'x2' => (float)$perm->jumlah_permintaan,
                    'y'  => (float)$p->jumlah_produksi,
                ];
            }
        }

        $coeffs = [
            'a' => $peramalan->a,
            'b1' => $peramalan->b1,
            'b2' => $peramalan->b2,
        ];

        $rows = [];
        $preds = [];
        $actuals = [];

        $mlr = app(\App\Services\MultipleLinearRegressionService::class);

        foreach ($data as $row) {
            $pred = $mlr->predict($coeffs, $row['x1'], $row['x2']);
            $preds[] = $pred;
            $actuals[] = $row['y'];
            $rows[] = [
                'bulan' => $row['bulan'],
                'x1' => $row['x1'],
                'x2' => $row['x2'],
                'actual' => $row['y'],
                'pred' => $pred,
            ];
        }

        $eval = $mlr->evaluate($actuals, $preds);

        return Pdf::loadView('forecast.pdf', [
            'rows' => $rows,
            'coeffs' => $coeffs,
            'forecast' => $peramalan,
            'eval' => $eval
        ])->download('forecast-' . $peramalan->forecast_bulan . '.pdf');
    }


    /**
     * Run forecast (POST)
     */
    public function run(Request $request)
    {
        $request->validate([
            'forecast_month' => 'required|string'
        ]);

        $input = $request->input('forecast_month');

        // normalize forecast_month -> first day of month
        try {
            if (preg_match('/^\d{4}-\d{2}$/', $input)) {
                // input like "2025-03" -> make "2025-03-01"
                $forecastMonth = Carbon::createFromFormat('Y-m-d', $input . '-01')->startOfDay();
            } else {
                $forecastMonth = Carbon::parse($input)->startOfDay();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['forecast_month' => 'Format bulan tidak valid.']);
        }

        // Build historical dataset: use produksi rows and match penjualan & permintaan by exact bulan
        $produksis = Produksi::orderBy('bulan')->get();

        $data = [];
        foreach ($produksis as $p) {
            // ensure $p->bulan treated as date string (format YYYY-MM-DD)
            $bulan = Carbon::parse($p->bulan)->format('Y-m-d');
            $pen  = Penjualan::where('bulan', $bulan)->first();
            $perm = Permintaan::where('bulan', $bulan)->first();

            if ($pen && $perm) {
                $data[] = [
                    'bulan' => $bulan,
                    'x1'    => (float)$pen->jumlah_penjualan,
                    'x2'    => (float)$perm->jumlah_permintaan,
                    'y'     => (float)$p->jumlah_produksi,
                ];
            }
        }

        // require at least 3 complete observations
        if (count($data) < 3) {
            return back()->withErrors(['data' => 'Data historis tidak cukup (minimal 3 bulan dengan semua variabel tersedia).']);
        }

        // -----------------------------
        // Hitung nilai-nilai Σ untuk detail perhitungan regresi (regcalc)
        // -----------------------------
        $sum_x1 = array_sum(array_column($data, 'x1'));
        $sum_x2 = array_sum(array_column($data, 'x2'));
        $sum_y  = array_sum(array_column($data, 'y'));

        $sum_x1_sq = array_sum(array_map(fn($r) => $r['x1'] * $r['x1'], $data));
        $sum_x2_sq = array_sum(array_map(fn($r) => $r['x2'] * $r['x2'], $data));

        $sum_x1_x2 = array_sum(array_map(fn($r) => $r['x1'] * $r['x2'], $data));
        $sum_x1_y  = array_sum(array_map(fn($r) => $r['x1'] * $r['y'], $data));
        $sum_x2_y  = array_sum(array_map(fn($r) => $r['x2'] * $r['y'], $data));

        // prepare input for service
        $fitInput = array_map(fn($r) => ['x1' => $r['x1'], 'x2' => $r['x2'], 'y' => $r['y']], $data);

        DB::beginTransaction();
        try {
            // fit model (may throw)
            $coeffs = $this->mlr->fit($fitInput);

            // compute in-sample predictions
            $preds = [];
            $actuals = [];
            $rows = [];
            foreach ($data as $row) {
                $pred = $this->mlr->predict($coeffs, $row['x1'], $row['x2']);
                $preds[] = $pred;
                $actuals[] = $row['y'];
                $rows[] = [
                    'bulan'  => $row['bulan'],
                    'x1'     => $row['x1'],
                    'x2'     => $row['x2'],
                    'actual' => $row['y'],
                    'pred'   => $pred,
                ];
            }

            // evaluation metrics
            $eval = $this->mlr->evaluate($actuals, $preds);

            // determine X1/X2 to use for next-month forecast: use latest available entries
            $latestPenjualan = Penjualan::orderBy('bulan', 'desc')->first();
            $latestPermintaan = Permintaan::orderBy('bulan', 'desc')->first();

            if (!$latestPenjualan || !$latestPermintaan) {
                DB::rollBack();
                return back()->withErrors(['data' => 'Tidak ada data penjualan/permintaan terbaru untuk menentukan X1/X2.']);
            }

            $x1_next = (float)$latestPenjualan->jumlah_penjualan;
            $x2_next = (float)$latestPermintaan->jumlah_permintaan;
            $y_forecast = $this->mlr->predict($coeffs, $x1_next, $x2_next);

            // periode data historis dipakai
            $periode_mulai = Carbon::parse($data[0]['bulan'])->startOfDay();
            $periode_akhir = Carbon::parse(end($data)['bulan'])->startOfDay();

            // simpan record peramalan
            $peramalan = Peramalan::create([
                'periode_mulai'  => $periode_mulai,
                'periode_akhir'  => $periode_akhir,
                'forecast_bulan' => $forecastMonth,
                'a'              => $coeffs['a'],
                'b1'             => $coeffs['b1'],
                'b2'             => $coeffs['b2'],
                'forecast_value' => $y_forecast,
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['regresi' => $e->getMessage()]);
        }

        // prepare regcalc array for view (detail Σ)
        $regcalc = [
            'sum_x1'     => $sum_x1,
            'sum_x2'     => $sum_x2,
            'sum_y'      => $sum_y,
            'sum_x1_sq'  => $sum_x1_sq,
            'sum_x2_sq'  => $sum_x2_sq,
            'sum_x1_x2'  => $sum_x1_x2,
            'sum_x1_y'   => $sum_x1_y,
            'sum_x2_y'   => $sum_x2_y,
            'n'          => count($data),
        ];

        // return view with everything the blade expects
        return view('forecast.index', [
            'coeffs'     => $coeffs,
            'rows'       => $rows,
            'eval'       => $eval,
            'regcalc'    => $regcalc,
            'evalDetail' => true,
            'forecast'   => [
                'forecast_month' => $forecastMonth->format('Y-m-d'),
                'x1'             => $x1_next,
                'x2'             => $x2_next,
                'y_pred'         => $y_forecast,
                'peramalan_id'   => $peramalan->id_peramalan, // sesuai PK: id_peramalan
            ],
        ]);
    }
}

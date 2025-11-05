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

class ForecastController extends Controller
{
    protected $mlr;

    public function __construct(MultipleLinearRegressionService $mlr)
    {
        $this->mlr = $mlr;
    }

    // show form
    public function index()
    {
        return view('forecast.index');
    }



    // run forecast (POST)
    public function run(Request $request)
    {
        $request->validate([
            'forecast_month' => 'required|string' // expects yyyy-mm (input type month) or yyyy-mm-dd
        ]);

        // normalize forecast_month -> first day of month
        $input = $request->input('forecast_month');
        try {
            // if user sends "2025-03" (from input[type=month]) -> append "-01"
            if (preg_match('/^\d{4}-\d{2}$/', $input)) {
                $forecastMonth = Carbon::createFromFormat('Y-m-d', $input . '-01')->startOfDay();
            } else {
                $forecastMonth = Carbon::parse($input)->startOfDay();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['forecast_month' => 'Format bulan tidak valid.']);
        }

        // Build historical dataset: we will use months that have all three values present
        // Strategy: get all produksi rows ordered by bulan, and for each bulan find matching penjualan & permintaan
        $produksis = Produksi::orderBy('bulan')->get();

        $data = []; // rows for regression
        foreach ($produksis as $p) {
            $bulan = Carbon::parse($p->bulan)->format('Y-m-d');  // FIX disini
            $pen = Penjualan::where('bulan', $bulan)->first();
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


        if (count($data) < 3) {
            return back()->withErrors(['data' => 'Data historis tidak cukup (minimal 3 bulan dengan semua variabel tersedia).']);
        }

        // prepare dataset for service
        $fitInput = array_map(function ($r) {
            return ['x1' => $r['x1'], 'x2' => $r['x2'], 'y' => $r['y']];
        }, $data);

        DB::beginTransaction();
        try {
            $coeffs = $this->mlr->fit($fitInput); // may throw
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['regresi' => $e->getMessage()]);
        }

        // compute in-sample predictions and evaluation
        $preds = [];
        $actuals = [];
        $rows = [];
        foreach ($data as $row) {
            $pred = $this->mlr->predict($coeffs, $row['x1'], $row['x2']);
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
        $eval = $this->mlr->evaluate($actuals, $preds);

        // For forecast 1 month ahead: we use the latest available penjualan & permintaan automatically (Opsi B)
        // Find latest month available in penjualan/permintaan (max by bulan)
        $latestPenjualan = Penjualan::orderBy('bulan', 'desc')->first();
        $latestPermintaan = Permintaan::orderBy('bulan', 'desc')->first();

        if (!$latestPenjualan || !$latestPermintaan) {
            DB::rollBack();
            return back()->withErrors(['data' => 'Tidak ada data penjualan/permintaan untuk menentukan X1/X2 bulan depan.']);
        }

        $x1_next = (float)$latestPenjualan->jumlah_penjualan;
        $x2_next = (float)$latestPermintaan->jumlah_permintaan;
        $y_forecast = $this->mlr->predict($coeffs, $x1_next, $x2_next);

        // save peramalan record (menyimpan a,b1,b2 + periode + forecast month + forecast_value)
        $periode_mulai = Carbon::parse($data[0]['bulan'])->startOfDay();
        $periode_akhir = Carbon::parse(end($data)['bulan'])->startOfDay();

        $peramalan = Peramalan::create([
            'periode_mulai' => $periode_mulai,
            'periode_akhir' => $periode_akhir,
            'forecast_bulan' => $forecastMonth,
            'a' => $coeffs['a'],
            'b1' => $coeffs['b1'],
            'b2' => $coeffs['b2'],
            'forecast_value' => $y_forecast,
        ]);

        DB::commit();

        // pass data to view
        return view('forecast.index', [
            'coeffs' => $coeffs,
            'rows' => $rows,
            'eval' => $eval,
            'forecast' => [
                'forecast_month' => $forecastMonth->format('Y-m-d'),
                'x1' => $x1_next,
                'x2' => $x2_next,
                'y_pred' => $y_forecast,
                'peramalan_id' => $peramalan->id_peramalan,
            ]
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\Permintaan;
use App\Models\Produksi;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Ambil data penjualan
        $penjualan = Penjualan::orderBy('bulan')->get(['bulan', 'jumlah_penjualan']);
        $permintaan = Permintaan::orderBy('bulan')->get(['bulan', 'jumlah_permintaan']);
        $produksi = Produksi::orderBy('bulan')->get(['bulan', 'jumlah_produksi']);

        return view('home', [
            'penjualan_bulan' => $penjualan->pluck('bulan'),
            'penjualan_jumlah' => $penjualan->pluck('jumlah_penjualan'),
            'permintaan_bulan' => $permintaan->pluck('bulan'),
            'permintaan_jumlah' => $permintaan->pluck('jumlah_permintaan'),
            'produksi_bulan' => $produksi->pluck('bulan'),
            'produksi_jumlah' => $produksi->pluck('jumlah_produksi'),
        ]);
    }
}

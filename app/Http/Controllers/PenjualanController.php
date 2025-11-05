<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;

class PenjualanController extends Controller
{
    public function index()
    {
        $data = Penjualan::orderBy('bulan', 'asc')->get();
        return view('penjualan.index', compact('data'));
    }

    public function create()
    {
        return view('penjualan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'bulan' => 'required|date',
            'jumlah_penjualan' => 'required|numeric'
        ]);

        Penjualan::create($request->all());
        return redirect()->route('penjualan.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = Penjualan::where('id_penjualan', $id)->first();
        return view('penjualan.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'bulan' => 'required|date',
            'jumlah_penjualan' => 'required|numeric'
        ]);

        Penjualan::where('id_penjualan', $id)->update([
            'bulan' => $request->bulan,
            'jumlah_penjualan' => $request->jumlah_penjualan
        ]);

        return redirect()->route('penjualan.index')->with('success', 'Data berhasil diperbarui');
    }

    public function destroy($id)
    {
        Penjualan::where('id_penjualan', $id)->delete();
        return redirect()->route('penjualan.index')->with('success', 'Data berhasil dihapus');
    }
}

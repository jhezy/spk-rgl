<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produksi;

class ProduksiController extends Controller
{
    public function index()
    {
        $data = Produksi::orderBy('bulan', 'asc')->get();
        return view('produksi.index', compact('data'));
    }

    public function create()
    {
        return view('produksi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'bulan' => 'required|date',
            'jumlah_produksi' => 'required|numeric'
        ]);

        Produksi::create($request->all());
        return redirect()->route('produksi.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = Produksi::where('id_produksi', $id)->first();
        return view('produksi.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'bulan' => 'required|date',
            'jumlah_produksi' => 'required|numeric'
        ]);

        Produksi::where('id_produksi', $id)->update([
            'bulan' => $request->bulan,
            'jumlah_produksi' => $request->jumlah_produksi
        ]);

        return redirect()->route('produksi.index')->with('success', 'Data berhasil diperbarui');
    }

    public function destroy($id)
    {
        Produksi::where('id_produksi', $id)->delete();
        return redirect()->route('produksi.index')->with('success', 'Data berhasil dihapus');
    }
}

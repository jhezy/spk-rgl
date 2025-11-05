<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permintaan;

class PermintaanController extends Controller
{
    public function index()
    {
        $data = Permintaan::orderBy('bulan', 'asc')->get();
        return view('permintaan.index', compact('data'));
    }

    public function create()
    {
        return view('permintaan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'bulan' => 'required|date',
            'jumlah_permintaan' => 'required|numeric'
        ]);

        Permintaan::create($request->all());
        return redirect()->route('permintaan.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = Permintaan::where('id_permintaan', $id)->first();
        return view('permintaan.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'bulan' => 'required|date',
            'jumlah_permintaan' => 'required|numeric'
        ]);

        Permintaan::where('id_permintaan', $id)->update([
            'bulan' => $request->bulan,
            'jumlah_permintaan' => $request->jumlah_permintaan
        ]);

        return redirect()->route('permintaan.index')->with('success', 'Data berhasil diperbarui');
    }

    public function destroy($id)
    {
        Permintaan::where('id_permintaan', $id)->delete();
        return redirect()->route('permintaan.index')->with('success', 'Data berhasil dihapus');
    }
}

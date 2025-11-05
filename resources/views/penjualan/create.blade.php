@extends('layouts.app')

@section('content')
<div class="container ">

    <h4>Tambah Data Penjualan</h4>
    <form action="{{ route('penjualan.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Bulan</label>
            <input type="date" name="bulan" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Jumlah Penjualan</label>
            <input type="number" name="jumlah_penjualan" class="form-control" required>
        </div>

        <button class="btn btn-success">Simpan</button>
        <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">Kembali</a>
    </form>

</div>
@endsection
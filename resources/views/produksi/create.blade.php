@extends('layouts.app')

@section('content')
<div class="container">

    <h4>Tambah Data Produksi</h4>
    <form action="{{ route('produksi.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Bulan</label>
            <input type="date" name="bulan" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Jumlah Produksi</label>
            <input type="number" name="jumlah_produksi" class="form-control" required>
        </div>

        <button class="btn btn-success">Simpan</button>
        <a href="{{ route('produksi.index') }}" class="btn btn-secondary">Kembali</a>
    </form>

</div>
@endsection
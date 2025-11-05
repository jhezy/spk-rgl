@extends('layouts.app')

@section('content')
<div class="container">

    <h4>Tambah Data Permintaan</h4>
    <form action="{{ route('permintaan.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Bulan</label>
            <input type="date" name="bulan" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Jumlah Permintaan</label>
            <input type="number" name="jumlah_permintaan" class="form-control" required>
        </div>

        <button class="btn btn-success">Simpan</button>
        <a href="{{ route('permintaan.index') }}" class="btn btn-secondary">Kembali</a>
    </form>

</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container">

    <h4>Edit Data Penjualan</h4>
    <form action="{{ route('penjualan.update',$data->id_penjualan) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Bulan</label>
            <input type="date" name="bulan" value="{{ $data->bulan }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Jumlah Penjualan</label>
            <input type="number" name="jumlah_penjualan" value="{{ $data->jumlah_penjualan }}" class="form-control" required>
        </div>

        <button class="btn btn-success">Update</button>
        <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">Kembali</a>
    </form>

</div>
@endsection
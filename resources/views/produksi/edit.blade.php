@extends('layouts.app')

@section('content')
<div class="container">

    <h4>Edit Data Produksi</h4>
    <form action="{{ route('produksi.update',$data->id_produksi) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Bulan</label>
            <input type="date" name="bulan" value="{{ $data->bulan }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Jumlah Produksi</label>
            <input type="number" name="jumlah_produksi" value="{{ $data->jumlah_produksi }}" class="form-control" required>
        </div>

        <button class="btn btn-success">Update</button>
        <a href="{{ route('produksi.index') }}" class="btn btn-secondary">Kembali</a>
    </form>

</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container">

    <h4>Edit Data Permintaan</h4>
    <form action="{{ route('permintaan.update',$data->id_permintaan) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Bulan</label>
            <input type="date" name="bulan" value="{{ $data->bulan }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Jumlah Permintaan</label>
            <input type="number" name="jumlah_permintaan" value="{{ $data->jumlah_permintaan }}" class="form-control" required>
        </div>

        <button class="btn btn-success">Update</button>
        <a href="{{ route('permintaan.index') }}" class="btn btn-secondary">Kembali</a>
    </form>

</div>
@endsection
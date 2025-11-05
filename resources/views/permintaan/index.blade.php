@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-semibold mb-0">Data Permintaan</h4>
            <small class="text-muted">Daftar seluruh data permintaan bulanan</small>
        </div>
        <a href="{{ route('permintaan.create') }}" class="btn btn-primary px-3">
            <i class="bi bi-plus-circle me-1"></i> Tambah Data
        </a>
    </div>

    {{-- Notifikasi sukses --}}
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Tabel data --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 20%">Bulan</th>
                            <th>Jumlah Permintaan</th>
                            <th class="text-center" style="width: 160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                        <tr>
                            <td class="fw-medium">
                                {{ \Carbon\Carbon::parse($item->bulan)->translatedFormat('F Y') }}
                            </td>
                            <td>{{ number_format($item->jumlah_permintaan) }}</td>
                            <td class="text-center">
                                <a href="{{ route('permintaan.edit', $item->id_permintaan) }}"
                                    class="btn btn-sm btn-outline-warning me-1">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>

                                <form action="{{ route('permintaan.destroy', $item->id_permintaan) }}"
                                    method="POST" class="d-inline"
                                    onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                Belum ada data permintaan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
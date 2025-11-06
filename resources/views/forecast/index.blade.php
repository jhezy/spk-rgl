@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-center align-items-center mb-4">
        <div class="text-center">
            <h2 class="fw-bold mb-0 text-primary">Forecasting Produksi Singkong CV. Anugerah Jaya Prima Abadi Sentosa</h2>
            <small class="text-muted">Metode: <strong>Regresi Linear Berganda</strong></small>
        </div>
    </div>

    {{-- Notifikasi Error --}}
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Terjadi kesalahan:</strong>
        <ul class="mb-0 ps-3 mt-2">
            @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif



    <div class="row g-4 mb-4">
        {{-- Form Forecast --}}
        <div class="col-lg-12 mx-auto">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-body p-4 bg-light text-center">
                    <!-- <div class="d-flex justify-content-center align-items-center mb-4">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-2">
                            <i class="bi bi-calendar-week fs-3"></i>
                        </div>
                        <h4 class="fw-bold mb-0 text-dark">Jalankan Forecast</h4>
                    </div> -->

                    <form action="{{ route('forecast.run') }}" method="POST" class="text-center">
                        @csrf

                        <div class="mb-4">
                            <label for="forecast_month" class="form-label fw-semibold text-muted">Bulan Target Forecast</label>
                            <input
                                type="month"
                                id="forecast_month"
                                name="forecast_month"
                                class="form-control form-control-lg text-center mx-auto shadow-sm-sm border-0 bg-white rounded-3"
                                style="max-width: 320px;"
                                required>
                            <div class="form-text text-muted mt-2">
                                Pilih bulan yang akan diprediksi. Nilai X₁ & X₂ diambil dari data terakhir.
                            </div>
                        </div>

                        <div class="mt-2">
                            <button type="submit"
                                class="btn btn-primary btn-lg w-100  rounded-3 shadow-sm d-flex justify-content-center align-items-center ">

                                <span>Proses Forecast</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        {{-- Hasil Forecast --}}
        @isset($forecast)
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-secondary text-white fw-semibold">
                    <i class="bi bi-lightbulb me-1"></i> Hasil Forecast
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0 align-middle">
                        <tbody>
                            <tr>
                                <th class="text-muted w-50 ps-4">Bulan</th>
                                <td><strong>{{ \Carbon\Carbon::parse($forecast['forecast_month'])->translatedFormat('F Y') }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted ps-4">X₁ (Penjualan)</th>
                                <td><strong>{{ $forecast['x1'] }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted ps-4">X₂ (Permintaan)</th>
                                <td><strong>{{ $forecast['x2'] }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted ps-4">Prediksi Y (Produksi)</th>
                                <td><strong class="text-primary">{{ number_format($forecast['y_pred']) }} </strong></td>
                            </tr>
                            <!-- <tr>
                                <th class="text-muted ps-4 small">ID Peramalan</th>
                                <td class="text-muted small">{{ $forecast['peramalan_id'] }}</td>
                            </tr> -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Koefisien Regresi --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-secondary text-white fw-semibold">
                    <i class="bi bi-calculator me-1"></i> Koefisien Regresi
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0 align-middle">
                        <tbody>
                            <tr>
                                <th class="text-muted w-50 ps-4">a (intercept)</th>
                                <td><strong>{{ number_format($coeffs['a'], 10) }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted ps-4">b₁ (Penjualan)</th>
                                <td><strong>{{ number_format($coeffs['b1'], 10) }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted ps-4">b₂ (Permintaan)</th>
                                <td><strong>{{ number_format($coeffs['b2'], 10) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Evaluasi Model --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-secondary text-white fw-semibold">
                    <i class="bi bi-bar-chart-line me-1"></i> Evaluasi Model
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0 align-middle">
                        <tbody>
                            <tr>
                                <th class="text-muted w-50 ps-4">MAD</th>
                                <td><strong>{{ number_format($eval['MAD'], 4) }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted ps-4">MSE</th>
                                <td><strong>{{ number_format($eval['MSE'], 4) }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted ps-4">MAPE</th>
                                <td>
                                    <strong>{{ $eval['MAPE'] !== null ? number_format($eval['MAPE'], 4).' %' : 'N/A' }}</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @endisset

    </div>

    <!-- {{-- Detail Perhitungan Regresi --}}
    @isset($regcalc)
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-light fw-semibold">
            <i class="bi bi-calculator"></i> Detail Perhitungan Regresi Linear Berganda
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th>Variabel</th>
                            <th>Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Σ X₁</td>
                            <td>{{ $regcalc['sum_x1'] }}</td>
                        </tr>
                        <tr>
                            <td>Σ X₂</td>
                            <td>{{ $regcalc['sum_x2'] }}</td>
                        </tr>
                        <tr>
                            <td>Σ Y</td>
                            <td>{{ $regcalc['sum_y'] }}</td>
                        </tr>
                        <tr>
                            <td>Σ X₁²</td>
                            <td>{{ $regcalc['sum_x1_sq'] }}</td>
                        </tr>
                        <tr>
                            <td>Σ X₂²</td>
                            <td>{{ $regcalc['sum_x2_sq'] }}</td>
                        </tr>
                        <tr>
                            <td>Σ X₁ * X₂</td>
                            <td>{{ $regcalc['sum_x1_x2'] }}</td>
                        </tr>
                        <tr>
                            <td>Σ X₁ * Y</td>
                            <td>{{ $regcalc['sum_x1_y'] }}</td>
                        </tr>
                        <tr>
                            <td>Σ X₂ * Y</td>
                            <td>{{ $regcalc['sum_x2_y'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div> -->


    <!-- {{-- Detail Evaluasi Model --}}
    @isset($evalDetail)
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-light fw-semibold">
            <i class="bi bi-bar-chart"></i> Detail Perhitungan Evaluasi Model
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th>Metode</th>
                            <th>Perhitungan</th>
                            <th>Hasil</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>MAD</td>
                            <td>Σ |Y - Ŷ| / n</td>
                            <td>{{ number_format($eval['MAD'], 6) }}</td>
                        </tr>
                        <tr>
                            <td>MSE</td>
                            <td>Σ (Y - Ŷ)² / n</td>
                            <td>{{ number_format($eval['MSE'], 6) }}</td>
                        </tr>
                        <tr>
                            <td>MAPE</td>
                            <td>Σ(|Y - Ŷ| / Y) × 100 / n</td>
                            <td>{{ $eval['MAPE'] !== null ? number_format($eval['MAPE'],6).' %' : 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endisset -->
    @endisset





    @isset($rows)
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-light fw-semibold d-flex align-items-center">
            <i class="bi bi-table me-2"></i> Data Historis & Prediksi
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Bulan</th>
                        <th>Penjualan (X₁)</th>
                        <th>Permintaan (X₂)</th>
                        <th>Produksi Aktual (Y)</th>
                        <th>Prediksi (Ŷ)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $r)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($r['bulan'])->translatedFormat('F Y') }}</td>
                        <td>{{ number_format($r['x1']) }}</td>
                        <td>{{ number_format($r['x2']) }}</td>
                        <td>{{ number_format($r['actual']) }}</td>
                        <td class="fw-semibold text-primary">{{ number_format($r['pred'],4) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    {{-- tombol export --}}
    <div class="p-3 text-center">
        <a href="{{ route('forecast.export') }}" class="btn btn-danger">Export PDF</a>


    </div>
    @endisset

</div>
@endsection


<style>
    .card-body.bg-light {
        background: #f8f9fa !important;
    }

    .form-control-lg {
        transition: all 0.2s ease-in-out;
    }

    .form-control-lg:focus {
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        border-color: #86b7fe;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        border: none;
        transition: all 0.2s ease-in-out;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #0b5ed7, #0948b3);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(13, 110, 253, 0.25);
    }

    .shadow-sm-sm {
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }
</style>
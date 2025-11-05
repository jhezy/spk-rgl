@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- ========== BAGIAN CHART ========== --}}
    <div class="row g-4 mb-4">
        {{-- Grafik Penjualan --}}
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-primary text-white fw-semibold">
                    <i class="bi bi-cart-check me-2"></i> Grafik Penjualan
                </div>
                <div class="card-body">
                    <canvas id="chartPenjualan" height="200"></canvas>
                </div>
            </div>
        </div>

        {{-- Grafik Permintaan --}}
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-success text-white fw-semibold">
                    <i class="bi bi-box-seam me-2"></i> Grafik Permintaan
                </div>
                <div class="card-body">
                    <canvas id="chartPermintaan" height="200"></canvas>
                </div>
            </div>
        </div>

        {{-- Grafik Produksi --}}
        <div class="col-lg-4 col-md-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-info text-white fw-semibold">
                    <i class="bi bi-gear-wide-connected me-2"></i> Grafik Produksi
                </div>
                <div class="card-body">
                    <canvas id="chartProduksi" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== FORM FORECAST ========== --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-body p-4 bg-light text-center">
            <div class="d-flex justify-content-center align-items-center mb-4">
                <div class=" text-primary rounded-circle p-3 me-3">
                    <i class="bi bi-calendar-week fs-3"></i>
                </div>
                <h4 class="fw-bold mb-0 text-dark">Jalankan Forecast</h4>
            </div>

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
                        class="btn btn-primary btn-lg w-100 rounded-3 shadow-sm d-flex justify-content-center align-items-center ">
                        <i class="bi bi-graph-up fs-5 me-2"></i>
                        <span>Proses Forecast</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========== HASIL FORECAST ========== --}}
    @isset($forecast)
    <div class="row g-4 mb-4">
        {{-- Hasil Forecast --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-info text-white fw-semibold">
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
                                <td><strong class="text-primary">{{ number_format($forecast['y_pred'], 4) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Koefisien Regresi --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-primary text-white fw-semibold">
                    <i class="bi bi-calculator me-1"></i> Koefisien Regresi
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0 align-middle">
                        <tbody>
                            <tr>
                                <th class="text-muted w-50 ps-4">a (intercept)</th>
                                <td><strong>{{ number_format($coeffs['a'], 6) }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted ps-4">b₁ (Penjualan)</th>
                                <td><strong>{{ number_format($coeffs['b1'], 6) }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted ps-4">b₂ (Permintaan)</th>
                                <td><strong>{{ number_format($coeffs['b2'], 6) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Evaluasi Model --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-success text-white fw-semibold">
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
                                <td><strong>{{ $eval['MAPE'] !== null ? number_format($eval['MAPE'], 4).' %' : 'N/A' }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== TABEL DATA HISTORIS & PREDIKSI ========== --}}
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
    @endisset
    @endisset
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        function createLineChart(ctxId, labels, data, color, title) {
            const ctx = document.getElementById(ctxId).getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: title,
                        data: data,
                        borderColor: color,
                        backgroundColor: color.replace('1)', '0.2)'),
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        },
                        x: {
                            ticks: {
                                color: '#666'
                            }
                        }
                    }
                }
            });
        }

        createLineChart('chartPenjualan', @json($penjualan_bulan), @json($penjualan_jumlah), 'rgba(13,110,253,1)', 'Penjualan');
        createLineChart('chartPermintaan', @json($permintaan_bulan), @json($permintaan_jumlah), 'rgba(25,135,84,1)', 'Permintaan');
        createLineChart('chartProduksi', @json($produksi_bulan), @json($produksi_jumlah), 'rgba(13,202,240,1)', 'Produksi');
    });
</script>
@endpush
<!DOCTYPE html>
<html>

<head>
    <title>Laporan Forecasting Produksi Singkong</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table,
        th,
        td {
            border: 1px solid #000;
            padding: 5px;
        }

        h3,
        h4 {
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body>

    <h3>HASIL PERAMALAN PRODUKSI SINGKONG</h3>
    <h4>Periode : {{ $forecast->forecast_bulan }}</h4>
    <hr>

    <h4>Persamaan Regresi :</h4>
    <p>Y = {{ $coeffs['a'] }} + {{ $coeffs['b1'] }}X1 + {{ $coeffs['b2'] }}X2</p>

    <br>

    <h4>Data History</h4>
    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th>X1 Penjualan</th>
                <th>X2 Permintaan</th>
                <th>Y Produksi Aktual</th>
                <th>Prediksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $r)
            <tr>
                <td>{{ $r['bulan'] }}</td>
                <td>{{ $r['x1'] }}</td>
                <td>{{ $r['x2'] }}</td>
                <td>{{ $r['actual'] }}</td>
                <td>{{ number_format($r['pred'],4) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <br>

    <table width="100%" style="margin-top:10px">
        <tr>
            <td width="50%" valign="top">
                <h4>Evaluasi Akurasi</h4>
                <table border="1" cellpadding="5" cellspacing="0" width="100%">
                    <tr>
                        <td>MAD</td>
                        <td>{{ number_format($eval['MAD'],4) }}</td>
                    </tr>
                    <tr>
                        <td>MSE</td>
                        <td>{{ number_format($eval['MSE'],4) }}</td>
                    </tr>
                    <tr>
                        <td>MAPE</td>
                        <td>{{ number_format($eval['MAPE'],4) }}%</td>
                    </tr>
                </table>
            </td>

            <td width="50%" valign="top">
                <h4>Koefisien Regresi</h4>
                <table border="1" cellpadding="5" cellspacing="0" width="100%">
                    <tr>
                        <td>a</td>
                        <td>{{ number_format($coeffs['a'],4) }}</td>
                    </tr>
                    <tr>
                        <td>b1</td>
                        <td>{{ number_format($coeffs['b1'],4) }}</td>
                    </tr>
                    <tr>
                        <td>b2</td>
                        <td>{{ number_format($coeffs['b2'],4) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>


    <br><br>

    <h4>Hasil Forecast Bulan : {{ $forecast->forecast_bulan }}</h4>
    <h3>Prediksi Produksi = {{ number_format($forecast->forecast_value) }}</h3>

</body>

</html>
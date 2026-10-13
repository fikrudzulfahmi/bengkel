<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Service Bulanan</title>
    <style>
        body { font-family: sans-serif; margin: 20px; color: #333; font-size: 14px; }
        h1, h2 { text-align: center; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; vertical-align: top; }
        th { background-color: #f4f4f4; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        @media print {
            body { margin: 0; }
            button { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <button onclick="window.print()" style="margin-bottom: 20px; padding: 10px 20px; cursor: pointer;">Cetak Laporan</button>

    <h1>Laporan Service Kendaraan</h1>
    <h2>Bulan: {{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</h2>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Tanggal</th>
                <th>Nama Pelanggan</th>
                <th>Merk/Tipe Motor</th>
                <th>Service yang Dilakukan</th>
                <th>Sparepart yang Digunakan</th>
                <th class="text-right">Total Biaya</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $trx)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $trx->created_at->format('d/m/Y') }}</td>
                <td>{{ $trx->customer ? $trx->customer->name : 'Umum' }}</td>
                <td>{{ $trx->motorcycle ? $trx->motorcycle->type . ' (' . $trx->motorcycle->plate_number . ')' : '-' }}</td>
                <td>
                    <ul style="margin:0; padding-left:15px;">
                    @foreach($trx->transactionServices as $ts)
                        <li>{{ $ts->service->name }} (Rp {{ number_format($ts->price, 0, ',', '.') }})</li>
                    @endforeach
                    </ul>
                </td>
                <td>
                    <ul style="margin:0; padding-left:15px;">
                    @foreach($trx->details as $detail)
                        <li>{{ $detail->sparepart->name }} x{{ $detail->quantity }} (Rp {{ number_format($detail->subtotal, 0, ',', '.') }})</li>
                    @endforeach
                    </ul>
                </td>
                <td class="text-right">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada data service pada bulan ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

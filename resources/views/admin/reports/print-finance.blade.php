<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Keuangan</title>
    <style>
        body { font-family: sans-serif; margin: 20px; color: #333; }
        h1, h2, h3 { text-align: center; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        @media print {
            body { margin: 0; }
            button { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <button onclick="window.print()" style="margin-bottom: 20px; padding: 10px 20px; cursor: pointer;">Cetak Laporan</button>

    <h1>Laporan Keuangan</h1>
    <h2>
        @if($type == 'monthly')
            Bulan: {{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}
        @else
            Tahun: {{ $year }}
        @endif
    </h2>

    <div style="margin-top: 30px;">
        <h3>1. Rincian Pendapatan Servis & Sparepart</h3>
        <table>
            <thead>
                <tr>
                    <th class="text-center" width="5%">No</th>
                    <th width="15%">Tanggal</th>
                    <th>Pelanggan</th>
                    <th class="text-right" width="25%">Total Biaya</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $index => $trx)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $trx->created_at->format('d/m/Y') }}</td>
                    <td>{{ $trx->customer ? $trx->customer->name : 'Umum' }}</td>
                    <td class="text-right">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data pendapatan servis.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 30px;">
        <h3>2. Rincian Pemasukan Lainnya</h3>
        <table>
            <thead>
                <tr>
                    <th class="text-center" width="5%">No</th>
                    <th width="15%">Tanggal</th>
                    <th>Keterangan</th>
                    <th class="text-right" width="25%">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pemasukanLain->values() as $index => $cb)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($cb->date)->format('d/m/Y') }}</td>
                    <td>{{ $cb->description }}</td>
                    <td class="text-right">Rp {{ number_format($cb->amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data pemasukan lainnya.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 30px;">
        <h3>3. Rincian Pengeluaran Operasional</h3>
        <table>
            <thead>
                <tr>
                    <th class="text-center" width="5%">No</th>
                    <th width="15%">Tanggal</th>
                    <th>Keterangan</th>
                    <th class="text-right" width="25%">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cashBooks->where('type', 'pengeluaran')->values() as $index => $cb)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($cb->date)->format('d/m/Y') }}</td>
                    <td>{{ $cb->description }}</td>
                    <td class="text-right">Rp {{ number_format($cb->amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data pengeluaran.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 40px; margin-bottom: 20px;">
        <h3 style="text-align: left;">Ringkasan Total</h3>
        <table style="width: 50%; float: right; margin-top: 0;">
            <tr>
                <td>Total Pendapatan Servis</td>
                <td class="text-right">Rp {{ number_format($totalIncomeServis, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Pemasukan Lain</td>
                <td class="text-right">Rp {{ number_format($totalIncomeLain, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Pengeluaran</td>
                <td class="text-right" style="color:red;">(Rp {{ number_format($totalPengeluaran, 0, ',', '.') }})</td>
            </tr>
            <tr class="font-bold" style="background-color: #f4f4f4;">
                <td>Laba Bersih</td>
                <td class="text-right">Rp {{ number_format($laba, 0, ',', '.') }}</td>
            </tr>
        </table>
        <div style="clear: both;"></div>
    </div>
    
    <div style="margin-top: 60px;">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; text-align: center; width: 33%;"></td>
                <td style="border: none; text-align: center; width: 33%;"></td>
                <td style="border: none; text-align: center; width: 33%;">
                    Mengetahui,<br><br><br><br><br>
                    <strong>( Admin / Pemilik )</strong>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

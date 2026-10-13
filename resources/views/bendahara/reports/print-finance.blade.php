<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Keuangan Bendahara</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; margin: 20px; color: #333; font-size: 13px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header h1 { font-size: 18px; font-weight: bold; margin-bottom: 4px; }
        .header h2 { font-size: 14px; font-weight: normal; color: #555; }
        .header .badge { display: inline-block; background: #f59e0b; color: white; font-size: 10px; font-weight: bold; padding: 2px 8px; border-radius: 999px; margin-top: 4px; letter-spacing: 0.5px; }
        .section { margin-top: 24px; }
        .section h3 { font-size: 13px; font-weight: bold; margin-bottom: 8px; border-left: 4px solid #f59e0b; padding-left: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ddd; padding: 7px 10px; text-align: left; }
        th { background-color: #fef3c7; font-weight: bold; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .summary-table { width: 45%; margin-left: auto; margin-top: 0; }
        .summary-table td { border: 1px solid #ddd; }
        .summary-total { background-color: #fef3c7; font-weight: bold; }
        .badge-pemasukan { color: #065f46; background: #d1fae5; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
        .badge-pengeluaran { color: #991b1b; background: #fee2e2; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
        .saldo-positive { color: #065f46; }
        .saldo-negative { color: #991b1b; }
        .signature { margin-top: 50px; }
        .signature-table { width: 100%; border: none; }
        .signature-table td { border: none; text-align: center; vertical-align: top; padding-top: 0; }
        .signature-line { border-top: 1px solid #333; width: 150px; margin: 0 auto; margin-top: 60px; }
        .no-data { text-align: center; color: #999; padding: 16px; font-style: italic; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <button onclick="window.print()" class="no-print" style="margin-bottom: 20px; padding: 8px 20px; cursor: pointer; background:#f59e0b; color:white; border:none; border-radius:6px; font-size:13px; font-weight:bold;">
        🖨️ Cetak Laporan
    </button>

    <div class="header">
        <div class="badge">KAS BENDAHARA</div>
        <h1>LAPORAN KEUANGAN BENDAHARA</h1>
        <h2>
            @if($type == 'monthly')
                Periode: {{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}
            @else
                Periode: Tahun {{ $year }}
            @endif
        </h2>
        <p style="font-size:11px; color:#888; margin-top:6px;">Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }}</p>
    </div>

    {{-- Rincian Setoran dari Mekanik --}}
    @if($approvedDeposits->count() > 0)
    <div class="section">
        <h3>1. Rincian Setoran dari Mekanik</h3>
        <table>
            <thead>
                <tr>
                    <th class="text-center" width="5%">No</th>
                    <th width="15%">Tanggal</th>
                    <th width="25%">Nama Mekanik</th>
                    <th>Keterangan</th>
                    <th class="text-right" width="20%">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($approvedDeposits as $index => $dep)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($dep->date)->format('d/m/Y') }}</td>
                    <td>{{ $dep->mechanic->name ?? '-' }}</td>
                    <td>{{ $dep->description }}</td>
                    <td class="text-right">Rp {{ number_format($dep->amount, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="font-bold" style="background:#f9fafb;">
                    <td colspan="4" class="text-right">Total Setoran Mekanik</td>
                    <td class="text-right">Rp {{ number_format($approvedDeposits->sum('amount'), 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    {{-- Rincian Pemasukan Lainnya --}}
    @php
        $pemasukanLain = $pemasukan->filter(fn($cb) => !str_starts_with($cb->description, 'Setoran dari'));
    @endphp
    <div class="section">
        <h3>{{ $approvedDeposits->count() > 0 ? '2' : '1' }}. Rincian Pemasukan Lainnya</h3>
        <table>
            <thead>
                <tr>
                    <th class="text-center" width="5%">No</th>
                    <th width="15%">Tanggal</th>
                    <th>Keterangan</th>
                    <th class="text-right" width="22%">Jumlah</th>
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
                    <td colspan="4" class="no-data">Tidak ada pemasukan lainnya.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Rincian Pengeluaran --}}
    <div class="section">
        <h3>{{ $approvedDeposits->count() > 0 ? '3' : '2' }}. Rincian Pengeluaran</h3>
        <table>
            <thead>
                <tr>
                    <th class="text-center" width="5%">No</th>
                    <th width="15%">Tanggal</th>
                    <th>Keterangan</th>
                    <th class="text-right" width="22%">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengeluaran->values() as $index => $cb)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($cb->date)->format('d/m/Y') }}</td>
                    <td>{{ $cb->description }}</td>
                    <td class="text-right">Rp {{ number_format($cb->amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="no-data">Tidak ada pengeluaran.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Ringkasan --}}
    <div class="section">
        <h3>Ringkasan Saldo Bendahara</h3>
        <table class="summary-table">
            <tr>
                <td>Total Setoran Mekanik</td>
                <td class="text-right">Rp {{ number_format($approvedDeposits->sum('amount'), 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Pemasukan Lainnya</td>
                <td class="text-right">Rp {{ number_format($pemasukanLain->sum('amount'), 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Pengeluaran</td>
                <td class="text-right" style="color: #991b1b;">(Rp {{ number_format($totalPengeluaran, 0, ',', '.') }})</td>
            </tr>
            <tr class="summary-total">
                <td>Saldo Kas Bendahara</td>
                <td class="text-right {{ $saldo >= 0 ? 'saldo-positive' : 'saldo-negative' }}">
                    Rp {{ number_format($saldo, 0, ',', '.') }}
                </td>
            </tr>
        </table>
        <div style="clear: both;"></div>
    </div>

    {{-- Tanda Tangan --}}
    <div class="signature">
        <table class="signature-table">
            <tr>
                <td style="width: 33%;"></td>
                <td style="width: 33%; text-align:center;">
                    Mengetahui,<br>
                    <div class="signature-line"></div>
                    <strong>( Admin / Pimpinan )</strong>
                </td>
                <td style="width: 33%; text-align:center;">
                    Bendahara,<br>
                    <div class="signature-line"></div>
                    <strong>( Bendahara )</strong>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

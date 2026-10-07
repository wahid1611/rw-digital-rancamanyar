@extends('layouts.admin')

@section('title', 'Laporan Keuangan RW')

@push('styles')
<style>
    /* Sembunyikan elemen print-only saat di layar */
    .print-only { display: none; }

    @media print {
        /* === SEMBUNYIKAN ELEMEN TIDAK PERLU === */
        .sidebar,
        .topbar,
        .btn,
        nav,
        .no-print { 
            display: none !important; 
        }

        .main-content { margin-left: 0 !important; }
        .content-area { padding: 0 !important; }

        /* === TAMPILKAN KOP RW + TTD === */
        .print-only { display: block !important; }

        /* === FONT LEBIH KECIL === */
        body { font-size: 10pt !important; }
        h2 { font-size: 16pt !important; }
        h3 { font-size: 14pt !important; }
        h4 { font-size: 11pt !important; }
        h5 { font-size: 13pt !important; }
        small { font-size: 9pt !important; }

        /* === CARD LEBIH RAPI === */
        .card {
            border: none !important;
            box-shadow: none !important;
            margin-bottom: 8px !important;
        }
        .card-body { padding: 5px !important; }
        .card-header { 
            background: #f0f0f0 !important; 
            padding: 4px 8px !important;
            font-size: 10pt !important;
        }

        /* === TABEL LEBIH RAPI === */
        table { 
            font-size: 9pt !important; 
            width: 100% !important;
            border-collapse: collapse !important;
        }
        th, td { 
            padding: 3px 5px !important; 
            border: 1px solid #ccc !important;
        }
        thead th {
            background: #eee !important;
            font-weight: bold !important;
        }
        tfoot th {
            background: #f8f8f8 !important;
        }

        /* === RINGKASAN STATISTIK === */
        .row.g-3.mb-3 .card-body {
            padding: 6px !important;
        }
    }
</style>
@endpush

@section('content')

{{-- ============ KOP RW (HANYA MUNCUL SAAT PRINT) ============ --}}
<div class="print-only">
    <div style="text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px;">
        <h2 style="margin: 0; font-weight: bold;">PENGURUS RUKUN WARGA 07</h2>
        <h3 style="margin: 3px 0;">PERUMAHAN RANCAMANYAR</h3>
        <p style="margin: 2px 0;">Desa Wancimekar, Kecamatan Kotabaru, Kabupaten Karawang</p>
        <p style="margin: 2px 0;">Jawa Barat — 41374</p>
    </div>
    <h4 style="text-align: center; margin: 15px 0; font-weight: bold; text-decoration: underline;">
        LAPORAN KEUANGAN RW
    </h4>
    <p style="text-align: center; margin: 5px 0 20px;">
        Periode: {{ \Carbon\Carbon::parse($dari)->format('d F Y') }} 
        s/d 
        {{ \Carbon\Carbon::parse($sampai)->format('d F Y') }}
    </p>
</div>

{{-- ============ HEADER HALAMAN (TAMPIL DI LAYAR) ============ --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Laporan Keuangan RW</h5>
        <small class="text-muted">Laporan uang masuk & keluar Kas RW</small>
    </div>
    <div class="d-flex gap-2">
        @can('keuangan_rw.export')
        <a href="{{ route('keuangan.rw.laporan.export', ['dari' => $dari, 'sampai' => $sampai]) }}" 
           class="btn btn-sm btn-success">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
        </a>
        @endcan
        <button onclick="window.print()" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-printer"></i> Cetak
        </button>
        <a href="{{ route('keuangan.rw.dashboard') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

{{-- ============ FILTER PERIODE (SEMBUNYI SAAT PRINT) ============ --}}
<div class="card border-0 shadow-sm mb-3 no-print">
    <div class="card-body">
        <form method="GET" action="{{ route('keuangan.rw.laporan') }}" class="row g-2">
            <div class="col-md-4">
                <label class="form-label small">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control form-control-sm" value="{{ $dari }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small">Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control form-control-sm" value="{{ $sampai }}" required>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-search"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============ RINGKASAN STATISTIK ============ --}}
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Uang Masuk</div>
                <h4 class="mb-0 text-success">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Uang Keluar</div>
                <h4 class="mb-0 text-danger">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Saldo Periode</div>
                <h4 class="mb-0 text-primary">Rp {{ number_format($saldo, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
</div>

{{-- ============ TABEL TRANSAKSI ============ --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <strong>Detail Transaksi ({{ $dari }} s/d {{ $sampai }})</strong>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th width="50">#</th>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Deskripsi</th>
                    <th>Dicatat Oleh</th>
                    <th class="text-end">Masuk</th>
                    <th class="text-end">Keluar</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksis as $i => $t)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><small>{{ $t->tgl_transaksi->format('d M Y') }}</small></td>
                    <td><small>{{ $t->kategori_label }}</small></td>
                    <td><small>{{ Str::limit($t->deskripsi, 50) }}</small></td>
                    <td><small>{{ $t->pencatat->name ?? '-' }}</small></td>
                    <td class="text-end">
                        @if ($t->jenis == 'pemasukan')
                            <strong class="text-success">Rp {{ number_format($t->nominal, 0, ',', '.') }}</strong>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($t->jenis == 'pengeluaran')
                            <strong class="text-danger">Rp {{ number_format($t->nominal, 0, ',', '.') }}</strong>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        Belum ada transaksi di periode ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if ($transaksis->count() > 0)
            <tfoot class="table-light">
                <tr>
                    <th colspan="5" class="text-end">TOTAL:</th>
                    <th class="text-end text-success">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</th>
                    <th class="text-end text-danger">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</th>
                </tr>
                <tr>
                    <th colspan="6" class="text-end">SALDO:</th>
                    <th class="text-end text-primary">Rp {{ number_format($saldo, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

{{-- ============ TANDA TANGAN (HANYA MUNCUL SAAT PRINT) ============ --}}
<div class="print-only" style="margin-top: 40px;">
    <div style="float: right; text-align: center; width: 250px;">
        <p style="margin: 3px 0;">Wancimekar, {{ now()->translatedFormat('d F Y') }}</p>
        <p style="margin: 3px 0;">Bendahara RW 07</p>
        <div style="height: 70px;"></div>
        <p style="margin: 3px 0; font-weight: bold; text-decoration: underline;">
            (..............................)
        </p>
    </div>
    <div style="clear: both;"></div>
</div>

{{-- ============ FOOTER PRINT ============ --}}
<div class="print-only" style="margin-top: 30px; padding-top: 8px; border-top: 1px solid #ccc; font-size: 8pt; color: #666; font-style: italic; text-align: center;">
    Dokumen ini dicetak dari Sistem Informasi RW Digital pada {{ now()->format('d F Y H:i') }}.
</div>

@endsection
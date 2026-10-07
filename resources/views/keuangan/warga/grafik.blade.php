@extends('layouts.admin')

@section('title', 'Grafik Keuangan RW')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Grafik Keuangan RW</h5>
        <small class="text-muted">Transparansi penggunaan uang iuran warga</small>
    </div>
    <a href="{{ route('keuangan.warga.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

{{-- Statistik Utama --}}
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
            <div class="card-body text-white">
                <div class="small opacity-75">Saldo Kas RW</div>
                <h4 class="mb-0">Rp {{ number_format($saldoRw, 0, ',', '.') }}</h4>
                <small>Uang RW saat ini</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body text-white">
                <div class="small opacity-75">Uang Masuk Bulan Ini</div>
                <h4 class="mb-0">Rp {{ number_format($pemasukanBulanIni, 0, ',', '.') }}</h4>
                <small>{{ now()->translatedFormat('F Y') }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);">
            <div class="card-body text-white">
                <div class="small opacity-75">Uang Keluar Bulan Ini</div>
                <h4 class="mb-0">Rp {{ number_format($pengeluaranBulanIni, 0, ',', '.') }}</h4>
                <small>{{ now()->translatedFormat('F Y') }}</small>
            </div>
        </div>
    </div>
</div>

{{-- Pengeluaran per Kategori --}}
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <strong>Uang Keluar per Kategori (Bulan Ini)</strong>
            </div>
            <div class="card-body">
                @if ($pengeluaranKategori->count() > 0)
                    @php $maxKeluar = $pengeluaranKategori->max() ?: 1; @endphp
                    @foreach ($pengeluaranKategori as $kat => $total)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <strong>{{ ucfirst(str_replace('_', ' ', $kat)) }}</strong>
                                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-danger" style="width: {{ ($total / $maxKeluar) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-center text-muted mb-0">Belum ada pengeluaran bulan ini.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <strong>Uang Masuk per Kategori (Bulan Ini)</strong>
            </div>
            <div class="card-body">
                @if ($pemasukanKategori->count() > 0)
                    @php $maxMasuk = $pemasukanKategori->max() ?: 1; @endphp
                    @foreach ($pemasukanKategori as $kat => $total)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <strong>{{ ucfirst(str_replace('_', ' ', $kat)) }}</strong>
                                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" style="width: {{ ($total / $maxMasuk) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-center text-muted mb-0">Belum ada pemasukan bulan ini.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Grafik 6 Bulan --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <strong>Tren Keuangan 6 Bulan Terakhir</strong>
    </div>
    <div class="card-body">
        @php
            $maxValue = max(array_merge(
                array_column($grafik, 'masuk'),
                array_column($grafik, 'keluar'),
                [1]
            ));
        @endphp
        @foreach ($grafik as $g)
            <div class="mb-3">
                <div class="d-flex justify-content-between small mb-1">
                    <strong>{{ $g['bulan'] }}</strong>
                    <span>
                        <span class="text-success">+Rp {{ number_format($g['masuk'], 0, ',', '.') }}</span>
                        |
                        <span class="text-danger">-Rp {{ number_format($g['keluar'], 0, ',', '.') }}</span>
                    </span>
                </div>
                <div class="progress mb-1" style="height: 8px;">
                    <div class="progress-bar bg-success" style="width: {{ ($g['masuk'] / $maxValue) * 100 }}%"></div>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-danger" style="width: {{ ($g['keluar'] / $maxValue) * 100 }}%"></div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="alert alert-info mt-3">
    <i class="bi bi-info-circle"></i>
    Halaman ini menampilkan <strong>ringkasan</strong> keuangan RW. Data detail (per warga) tidak ditampilkan untuk menjaga privasi.
</div>

@endsection
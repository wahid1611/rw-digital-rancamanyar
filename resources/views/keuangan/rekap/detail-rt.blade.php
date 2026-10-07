@extends('layouts.admin')

@section('title', 'Detail Keuangan ' . $rt->nama_rt)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Detail Keuangan {{ $rt->nama_rt }}</h5>
        <small class="text-muted">Mode read-only — hanya lihat</small>
    </div>
    <a href="{{ route('keuangan.rekap.dashboard') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

{{-- Ringkasan --}}
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Uang Masuk</div>
                <h5 class="mb-0 text-success">Rp {{ number_format($pemasukan, 0, ',', '.') }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Uang Keluar</div>
                <h5 class="mb-0 text-danger">Rp {{ number_format($pengeluaran, 0, ',', '.') }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Saldo</div>
                <h5 class="mb-0 text-primary">Rp {{ number_format($saldo, 0, ',', '.') }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Belum Bayar</div>
                <h5 class="mb-0 text-warning">{{ $belumBayar->count() }} KK</h5>
            </div>
        </div>
    </div>
</div>

{{-- Transaksi Terbaru --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white">
        <strong>Transaksi Terbaru (20 terakhir)</strong>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Deskripsi</th>
                    <th>Dicatat Oleh</th>
                    <th class="text-end">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksis as $t)
                <tr>
                    <td><small>{{ $t->tgl_transaksi->format('d M Y') }}</small></td>
                    <td><small>{{ $t->kategori_label }}</small></td>
                    <td><small>{{ Str::limit($t->deskripsi, 60) }}</small></td>
                    <td><small>{{ $t->pencatat->name ?? '-' }}</small></td>
                    <td class="text-end">
                        <strong class="{{ $t->jenis == 'pemasukan' ? 'text-success' : 'text-danger' }}">
                            {{ $t->jenis == 'pemasukan' ? '+' : '-' }} Rp {{ number_format($t->nominal, 0, ',', '.') }}
                        </strong>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-3 text-muted">Belum ada transaksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Warga Belum Bayar --}}
@if ($belumBayar->count() > 0)
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <strong>Warga Belum Bayar ({{ $belumBayar->count() }} KK)</strong>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nama KK</th>
                    <th>Iuran</th>
                    <th>Periode</th>
                    <th class="text-end">Tunggakan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($belumBayar as $b)
                <tr>
                    <td><strong>{{ $b->keluarga->kepala_keluarga_nama ?? '-' }}</strong></td>
                    <td><small>{{ $b->iuran->nama ?? '-' }}</small></td>
                    <td><span class="badge bg-secondary">{{ $b->periode }}</span></td>
                    <td class="text-end text-danger">
                        <strong>Rp {{ number_format($b->tunggakan, 0, ',', '.') }}</strong>
                    </td>
                    <td><span class="badge {{ $b->status_badge }}">{{ $b->status_label }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
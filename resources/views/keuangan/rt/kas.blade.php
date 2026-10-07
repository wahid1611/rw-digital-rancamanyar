@extends('layouts.admin')

@section('title', 'Kas ' . $rt->nama_rt)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Kas {{ $rt->nama_rt }}</h5>
        <small class="text-muted">Riwayat uang masuk & keluar {{ $rt->nama_rt }}</small>
    </div>
    <a href="{{ route('keuangan.rt.dashboard') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

{{-- Ringkasan --}}
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Uang Masuk</div>
                <h5 class="mb-0 text-success">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Uang Keluar</div>
                <h5 class="mb-0 text-danger">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Saldo Kas {{ $rt->nama_rt }}</div>
                <h5 class="mb-0 text-primary">Rp {{ number_format($saldo, 0, ',', '.') }}</h5>
            </div>
        </div>
    </div>
</div>

{{-- Tombol Aksi --}}
<div class="d-flex gap-2 mb-3">
    @can('keuangan_rt.create')
    <a href="{{ route('keuangan.rt.masuk.create') }}" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Catat Uang Masuk
    </a>
    <a href="{{ route('keuangan.rt.keluar.create') }}" class="btn btn-danger">
        <i class="bi bi-dash-circle"></i> Catat Uang Keluar
    </a>
    @endcan

    @can('keuangan_rt.export')
<a href="{{ route('keuangan.rt.kas.export', ['tab' => $tab]) }}" class="btn btn-outline-success ms-auto">
    <i class="bi bi-download"></i> Export Excel
</a>
@endcan
</div>

{{-- Tab --}}
<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ $tab == 'masuk' ? 'active' : '' }}" href="{{ route('keuangan.rt.kas', ['tab' => 'masuk']) }}">
            <i class="bi bi-arrow-down-circle text-success"></i> Uang Masuk
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $tab == 'keluar' ? 'active' : '' }}" href="{{ route('keuangan.rt.kas', ['tab' => 'keluar']) }}">
            <i class="bi bi-arrow-up-circle text-danger"></i> Uang Keluar
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('keuangan.rt.belum-bayar') }}">
            <i class="bi bi-people text-warning"></i> Belum Bayar
        </a>
    </li>
</ul>

{{-- Tabel Transaksi --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th width="50">#</th>
                    <th>Kode</th>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Deskripsi</th>
                    <th class="text-end">Nominal</th>
                    <th width="80" class="text-center">Bukti</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksis as $i => $t)
                <tr>
                    <td>{{ $transaksis->firstItem() + $i }}</td>
                    <td><code style="font-size: 12px;">{{ $t->kode_transaksi }}</code></td>
                    <td><small>{{ $t->tgl_transaksi->format('d M Y') }}</small></td>
                    <td>
                        @if ($t->kategori == 'iuran') <span class="badge bg-info text-dark">Iuran</span>
                        @elseif ($t->kategori == 'sumbangan') <span class="badge bg-success">Sumbangan</span>
                        @elseif ($t->kategori == 'bantuan') <span class="badge bg-primary">Bantuan</span>
                        @elseif ($t->kategori == 'denda') <span class="badge bg-warning text-dark">Denda</span>
                        @elseif ($t->kategori == 'operasional') <span class="badge bg-secondary">Operasional</span>
                        @elseif ($t->kategori == 'perbaikan') <span class="badge bg-warning text-dark">Perbaikan</span>
                        @elseif ($t->kategori == 'kegiatan') <span class="badge bg-info text-dark">Kegiatan</span>
                        @elseif ($t->kategori == 'sosial') <span class="badge bg-danger">Sosial</span>
                        @else <span class="badge bg-secondary">{{ $t->kategori }}</span>
                        @endif
                    </td>
                    <td><small>{{ Str::limit($t->deskripsi, 60) }}</small></td>
                    <td class="text-end">
                        <strong class="{{ $t->jenis == 'pemasukan' ? 'text-success' : 'text-danger' }}">
                            {{ $t->jenis == 'pemasukan' ? '+' : '-' }} Rp {{ number_format($t->nominal, 0, ',', '.') }}
                        </strong>
                    </td>
                    <td class="text-center">
                        @if ($t->bukti)
                        <a href="{{ route('file.preview', $t->bukti) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Lihat Bukti">
                            <i class="bi bi-image"></i>
                        </a>
                        @else
                        <small class="text-muted">-</small>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox" style="font-size: 40px;"></i>
                        <p class="mb-0 mt-2">Belum ada transaksi.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($transaksis->hasPages())
    <div class="card-footer bg-white">{{ $transaksis->links() }}</div>
    @endif
</div>

@endsection
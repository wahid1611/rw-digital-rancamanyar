@extends('layouts.admin')

@section('title', 'Buku Kas RW')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Buku Kas RW</h5>
        <small class="text-muted">Total: {{ $transaksis->total() }} transaksi</small>
    </div>
    @can('kas.create')
    <a href="{{ route('keuangan.kas.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Catat Transaksi
    </a>
    @endcan
</div>

{{-- Statistik --}}
<div class="row g-2 mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Total Pemasukan</small>
                <h5 class="mb-0 text-success">Rp {{ number_format($stats['masuk'], 0, ',', '.') }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Total Pengeluaran</small>
                <h5 class="mb-0 text-danger">Rp {{ number_format($stats['keluar'], 0, ',', '.') }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Saldo Kas</small>
                <h5 class="mb-0 text-primary">Rp {{ number_format($stats['saldo'], 0, ',', '.') }}</h5>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('keuangan.kas.index') }}" class="row g-2">
            <div class="col-md-2">
                <select name="jenis" class="form-select form-select-sm">
                    <option value="">Semua Jenis</option>
                    <option value="pemasukan" {{ request('jenis') == 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                    <option value="pengeluaran" {{ request('jenis') == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="dari" class="form-control form-control-sm" value="{{ request('dari') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="sampai" class="form-control form-control-sm" value="{{ request('sampai') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i> Filter</button>
                <a href="{{ route('keuangan.kas.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
            </div>
        </form>
    </div>
</div>

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
                    <th>Jenis</th>
                    <th>Nominal</th>
                    <th width="80" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksis as $i => $t)
                <tr>
                    <td>{{ $transaksis->firstItem() + $i }}</td>
                    <td><code>{{ $t->kode_transaksi }}</code></td>
                    <td><small>{{ $t->tgl_transaksi->format('d M Y') }}</small></td>
                    <td><span class="badge bg-info text-dark">{{ $t->kategori_label }}</span></td>
                    <td><small>{{ Str::limit($t->deskripsi, 60) }}</small></td>
                    <td><span class="badge {{ $t->jenis_badge }}">{{ ucfirst($t->jenis) }}</span></td>
                    <td>
                        <strong class="{{ $t->jenis === 'pemasukan' ? 'text-success' : 'text-danger' }}">
                            {{ $t->jenis === 'pemasukan' ? '+' : '-' }} Rp {{ number_format($t->nominal, 0, ',', '.') }}
                        </strong>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('keuangan.kas.show', $t) }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
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
@extends('layouts.admin')

@section('title', 'Daftar Pembayaran')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1"> Daftar Pembayaran</h5>
        <small class="text-muted">Total: {{ $pembayarans->total() }} pembayaran</small>
    </div>
    @can('tagihan.bayar')
    <a href="{{ route('keuangan.pembayaran.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Catat Pembayaran
    </a>
    @endcan
</div>

{{-- Statistik --}}
<div class="row g-2 mb-3">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Total Hari Ini</small>
                <h5 class="mb-0 text-primary">Rp {{ number_format($totalHariIni, 0, ',', '.') }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Total Bulan Ini</small>
                <h5 class="mb-0 text-success">Rp {{ number_format($totalBulanIni, 0, ',', '.') }}</h5>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('keuangan.pembayaran.index') }}" class="row g-2">
            <div class="col-md-3">
                <select name="metode" class="form-select form-select-sm">
                    <option value="">Semua Metode</option>
                    @foreach (['tunai' => 'Tunai', 'transfer' => 'Transfer', 'qris' => 'QRIS', 'ewallet' => 'E-Wallet'] as $v => $l)
                        <option value="{{ $v }}" {{ request('metode') == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="dari" class="form-control form-control-sm" value="{{ request('dari') }}" placeholder="Dari tanggal">
            </div>
            <div class="col-md-3">
                <input type="date" name="sampai" class="form-control form-control-sm" value="{{ request('sampai') }}" placeholder="Sampai tanggal">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i> Filter</button>
                <a href="{{ route('keuangan.pembayaran.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
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
                    <th>Keluarga</th>
                    <th>Iuran</th>
                    <th>Nominal</th>
                    <th>Metode</th>
                    <th>Tanggal</th>
                    <th>Dicatat Oleh</th>
                    <th width="80" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pembayarans as $i => $p)
                <tr>
                    <td>{{ $pembayarans->firstItem() + $i }}</td>
                    <td><code>{{ $p->kode_pembayaran }}</code></td>
                    <td>{{ $p->keluarga->kepala_keluarga_nama ?? '-' }}</td>
                    <td><small>{{ $p->tagihan->iuran->nama ?? '-' }}</small></td>
                    <td class="text-success"><strong>Rp {{ number_format($p->nominal, 0, ',', '.') }}</strong></td>
                    <td><span class="badge bg-secondary">{{ $p->metode_label }}</span></td>
                    <td><small>{{ $p->tgl_bayar->format('d M Y') }}</small></td>
                    <td><small>{{ $p->pencatat->name ?? '-' }}</small></td>
                    <td class="text-center">
                        <a href="{{ route('keuangan.pembayaran.show', $p) }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox" style="font-size: 40px;"></i>
                        <p class="mb-0 mt-2">Belum ada pembayaran.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($pembayarans->hasPages())
    <div class="card-footer bg-white">{{ $pembayarans->links() }}</div>
    @endif
</div>

@endsection
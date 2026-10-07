@extends('layouts.admin')

@section('title', 'Warga Belum Bayar ' . $rt->nama_rt)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Warga Belum Bayar</h5>
        <small class="text-muted">Daftar KK {{ $rt->nama_rt }} yang belum bayar iuran</small>
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
                <div class="text-muted small">Total KK</div>
                <h5 class="mb-0">{{ $totalKK }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Sudah Bayar</div>
                <h5 class="mb-0 text-success">{{ $totalBayar }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Belum Bayar</div>
                <h5 class="mb-0 text-danger">{{ $belumBayars->total() }}</h5>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('keuangan.rt.belum-bayar') }}" class="row g-2">
            <div class="col-md-4">
                <label class="form-label small">Periode</label>
                <input type="month" name="periode" class="form-control form-control-sm" value="{{ $periode }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small">Iuran</label>
                <select name="iuran_id" class="form-select form-select-sm">
                    <option value="">Semua Iuran</option>
                    @foreach ($iurans as $i)
                        <option value="{{ $i->id }}" {{ $iuranId == $i->id ? 'selected' : '' }}>
                            {{ $i->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-search"></i> Tampilkan
                </button>
                <a href="{{ route('keuangan.rt.belum-bayar') }}" class="btn btn-sm btn-outline-secondary ms-2">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th width="50">#</th>
                    <th>Nama KK</th>
                    <th>Iuran</th>
                    <th>Periode</th>
                    <th class="text-end">Tunggakan</th>
                    <th>Status</th>
                    <th width="80" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($belumBayars as $i => $t)
                <tr>
                    <td>{{ $belumBayars->firstItem() + $i }}</td>
                    <td>
                        <strong>{{ $t->keluarga->kepala_keluarga_nama ?? '-' }}</strong>
                        <br><small class="text-muted">{{ $t->keluarga->no_kk ?? '-' }}</small>
                    </td>
                    <td><small>{{ $t->iuran->nama ?? '-' }}</small></td>
                    <td><span class="badge bg-secondary">{{ $t->periode }}</span></td>
                    <td class="text-end"><strong class="text-danger">Rp {{ number_format($t->tunggakan, 0, ',', '.') }}</strong></td>
                    <td><span class="badge {{ $t->status_badge }}">{{ $t->status_label }}</span></td>
                    <td class="text-center">
                        @can('tagihan.bayar')
                        <a href="{{ route('keuangan.pembayaran.create', ['tagihan_id' => $t->id]) }}"
                           class="btn btn-sm btn-success" title="Catat Bayar">
                            <i class="bi bi-cash"></i>
                        </a>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        <i class="bi bi-check-circle text-success" style="font-size: 40px;"></i>
                        <p class="mb-0 mt-2">Semua KK sudah bayar!</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($belumBayars->hasPages())
    <div class="card-footer bg-white">{{ $belumBayars->links() }}</div>
    @endif
</div>

@endsection
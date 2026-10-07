@extends('layouts.admin')

@section('title', 'Daftar Tagihan')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">📄 Daftar Tagihan</h5>
        <small class="text-muted">Total: {{ $tagihans->total() }} tagihan</small>
    </div>
    @can('tagihan.generate')
    <a href="{{ route('keuangan.tagihan.create') }}" class="btn btn-primary">
        <i class="bi bi-magic"></i> Generate Tagihan
    </a>
    @endcan
</div>

{{-- Statistik --}}
<div class="row g-2 mb-3">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Total Tagihan</small>
                <h5 class="mb-0">{{ $stats['total'] }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Belum Bayar</small>
                <h5 class="mb-0 text-danger">{{ $stats['belum_bayar'] }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Lunas</small>
                <h5 class="mb-0 text-success">{{ $stats['lunas'] }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Total Tunggakan</small>
                <h5 class="mb-0 text-warning">Rp {{ number_format($stats['tunggakan'], 0, ',', '.') }}</h5>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('keuangan.tagihan.index') }}" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="q" class="form-control form-control-sm" 
                       placeholder="Cari nama KK / No KK..." value="{{ request('q') }}">
            </div>
            <div class="col-md-2">
                <select name="iuran_id" class="form-select form-select-sm">
                    <option value="">Semua Iuran</option>
                    @foreach ($iurans as $iu)
                        <option value="{{ $iu->id }}" {{ request('iuran_id') == $iu->id ? 'selected' : '' }}>{{ $iu->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach (['belum_bayar' => 'Belum Bayar', 'sebagian' => 'Sebagian', 'lunas' => 'Lunas', 'telat' => 'Telat', 'batal' => 'Batal'] as $v => $l)
                        <option value="{{ $v }}" {{ request('status') == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" name="periode" class="form-control form-control-sm" 
                       placeholder="Periode (2026-10)" value="{{ request('periode') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i> Filter</button>
                <a href="{{ route('keuangan.tagihan.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
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
                    <th>Periode</th>
                    <th>Nominal</th>
                    <th>Dibayar</th>
                    <th>Tunggakan</th>
                    <th>Status</th>
                    <th width="80" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tagihans as $i => $t)
                <tr>
                    <td>{{ $tagihans->firstItem() + $i }}</td>
                    <td><code>{{ $t->kode_tagihan }}</code></td>
                    <td>
                        <strong>{{ $t->keluarga->kepala_keluarga_nama ?? '-' }}</strong>
                        <br><small class="text-muted">{{ $t->rt->nama_rt ?? '-' }}</small>
                    </td>
                    <td><small>{{ $t->iuran->nama ?? '-' }}</small></td>
                    <td><span class="badge bg-info text-dark">{{ $t->periode }}</span></td>
                    <td>Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                    <td class="text-success">Rp {{ number_format($t->total_dibayar, 0, ',', '.') }}</td>
                    <td class="text-danger"><strong>Rp {{ number_format($t->tunggakan, 0, ',', '.') }}</strong></td>
                    <td><span class="badge {{ $t->status_badge }}">{{ $t->status_label }}</span></td>
                    <td class="text-center">
                        <a href="{{ route('keuangan.tagihan.show', $t) }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox" style="font-size: 40px;"></i>
                        <p class="mb-0 mt-2">Belum ada tagihan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($tagihans->hasPages())
    <div class="card-footer bg-white">{{ $tagihans->links() }}</div>
    @endif
</div>

@endsection
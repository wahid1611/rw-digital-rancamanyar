@extends('layouts.admin')

@section('title', 'UMKM Warga')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">UMKM Warga</h5>
        <small class="text-muted">Usaha Mikro, Kecil, dan Menengah warga RW 07</small>
    </div>
    @can('umkm.create')
    <a href="{{ route('umkm.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Daftarkan UMKM
    </a>
    @endcan
</div>

{{-- Statistik --}}
@if (auth()->user()->hasAnyRole(['super_admin', 'ketua_rw', 'ketua_rt', 'sekretaris']))
<div class="row g-2 mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Total UMKM</small>
                <h5 class="mb-0">{{ $stats['total'] }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Aktif</small>
                <h5 class="mb-0 text-success">{{ $stats['aktif'] }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Menunggu Verifikasi</small>
                <h5 class="mb-0 text-warning">{{ $stats['pending'] }}</h5>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('umkm.index') }}" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control form-control-sm"
                       placeholder="Cari nama usaha..." value="{{ request('q') }}">
            </div>
            <div class="col-md-3">
                <select name="kategori" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    @foreach (['makanan' => 'Makanan', 'minuman' => 'Minuman', 'jasa' => 'Jasa', 'fashion' => 'Fashion', 'kerajinan' => 'Kerajinan', 'pertanian' => 'Pertanian', 'elektronik' => 'Elektronik', 'lainnya' => 'Lainnya'] as $v => $l)
                        <option value="{{ $v }}" {{ request('kategori') == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="rt_id" class="form-select form-select-sm">
                    <option value="">Semua RT</option>
                    @foreach ($rts as $rt)
                        <option value="{{ $rt->id }}" {{ request('rt_id') == $rt->id ? 'selected' : '' }}>{{ $rt->nama_rt }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i> Filter</button>
                <a href="{{ route('umkm.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
            </div>
        </form>
    </div>
</div>

{{-- Grid UMKM --}}
<div class="row g-3">
    @forelse ($umkms as $u)
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            @if ($u->foto_usaha_url)
                <img src="{{ $u->foto_usaha_url }}" class="card-img-top" style="height: 180px; object-fit: cover;">
            @else
                <div class="d-flex align-items-center justify-content-center" style="height: 180px; background: {{ $u->kategori_color }}20;">
                    <i class="bi bi-shop" style="font-size: 60px; color: {{ $u->kategori_color }};"></i>
                </div>
            @endif

            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge" style="background: {{ $u->kategori_color }}; color: #fff;">{{ $u->kategori_label }}</span>
                    @if ($u->status !== 'aktif')
                        <span class="badge {{ $u->status_badge }}">{{ $u->status_label }}</span>
                    @endif
                </div>

                <h6 class="mb-1">
                    <a href="{{ route('umkm.show', $u) }}" class="text-decoration-none">{{ $u->nama_usaha }}</a>
                </h6>
                <p class="text-muted small mb-2">
                    <i class="bi bi-person"></i> {{ $u->user->name ?? '-' }}
                    • {{ $u->rt->nama_rt ?? '-' }}
                </p>
                <p class="small text-muted">{{ Str::limit($u->deskripsi, 80) }}</p>

                @if ($u->jam_operasional)
                    <p class="small mb-0"><i class="bi bi-clock"></i> {{ $u->jam_operasional }}</p>
                @endif
            </div>

            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                <small class="text-muted"><i class="bi bi-box"></i> {{ $u->produks_count }} produk</small>
                <a href="{{ route('umkm.show', $u) }}" class="btn btn-sm btn-primary">
                    Lihat <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-shop" style="font-size: 60px;"></i>
                <p class="mb-0 mt-2">Belum ada UMKM terdaftar.</p>
            </div>
        </div>
    </div>
    @endforelse
</div>

@if ($umkms->hasPages())
<div class="mt-3">{{ $umkms->links() }}</div>
@endif

@endsection
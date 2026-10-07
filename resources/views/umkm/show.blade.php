@extends('layouts.admin')

@section('title', $umkm->nama_usaha)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">{{ $umkm->nama_usaha }}</h5>
        <small class="text-muted">
            <span class="badge" style="background: {{ $umkm->kategori_color }};">{{ $umkm->kategori_label }}</span>
            <span class="badge {{ $umkm->status_badge }}">{{ $umkm->status_label }}</span>
        </small>
    </div>
    <div>
        @if ($umkm->user_id === auth()->id() || auth()->user()->hasAnyRole(['super_admin', 'ketua_rw', 'ketua_rt']))
        <a href="{{ route('umkm.edit', $umkm) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil"></i> Edit
        </a>
        @endif
        <a href="{{ route('umkm.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3">
    {{-- Info UMKM --}}
    <div class="col-md-4">
        @if ($umkm->foto_usaha_url)
            <img src="{{ $umkm->foto_usaha_url }}" class="img-fluid rounded shadow-sm mb-3">
        @elseif ($umkm->logo_url)
            <img src="{{ $umkm->logo_url }}" class="img-fluid rounded shadow-sm mb-3">
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-center mb-3">
                    @if ($umkm->logo_url)
                        <img src="{{ $umkm->logo_url }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%;">
                    @else
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px; background: {{ $umkm->kategori_color }}20;">
                            <i class="bi bi-shop" style="font-size: 40px; color: {{ $umkm->kategori_color }};"></i>
                        </div>
                    @endif
                </div>
                <table class="table table-sm mb-0">
                    <tr><th>Pemilik</th><td>{{ $umkm->user->name ?? '-' }}</td></tr>
                    <tr><th>RT</th><td>{{ $umkm->rt->nama_rt ?? '-' }}</td></tr>
                    <tr><th>Alamat</th><td>{{ $umkm->alamat ?? '-' }}</td></tr>
                    @if ($umkm->jam_operasional)
                    <tr><th>Jam Buka</th><td>{{ $umkm->jam_operasional }}</td></tr>
                    @endif
                    <tr><th>Views</th><td>{{ $umkm->views }}</td></tr>
                </table>
            </div>
        </div>

        {{-- Kontak --}}
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white"><strong><i class="bi bi-telephone"></i> Kontak</strong></div>
            <div class="card-body">
                @if ($umkm->whatsapp_url)
                    <a href="{{ $umkm->whatsapp_url }}" target="_blank" class="btn btn-success btn-sm w-100 mb-2">
                        <i class="bi bi-whatsapp"></i> WhatsApp
                    </a>
                @endif
                @if ($umkm->no_hp)
                    <a href="tel:{{ $umkm->no_hp }}" class="btn btn-outline-primary btn-sm w-100 mb-2">
                        <i class="bi bi-telephone"></i> {{ $umkm->no_hp }}
                    </a>
                @endif
                @if ($umkm->email)
                    <a href="mailto:{{ $umkm->email }}" class="btn btn-outline-secondary btn-sm w-100 mb-2">
                        <i class="bi bi-envelope"></i> Email
                    </a>
                @endif
                @if ($umkm->instagram)
                    <a href="https://instagram.com/{{ ltrim($umkm->instagram, '@') }}" target="_blank" class="btn btn-outline-danger btn-sm w-100">
                        <i class="bi bi-instagram"></i> {{ $umkm->instagram }}
                    </a>
                @endif
            </div>
        </div>

        {{-- Verifikasi (jika RT/RW) --}}
        @can('umkm.verifikasi')
        @if ($umkm->status === 'pending')
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-warning text-dark"><strong><i class="bi bi-shield-check"></i> Verifikasi</strong></div>
            <div class="card-body">
                <form method="POST" action="{{ route('umkm.verifikasi', $umkm) }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small">Status</label>
                        <select name="status" class="form-select form-select-sm" required>
                            <option value="aktif">Setujui (Aktif)</option>
                            <option value="nonaktif">Nonaktifkan</option>
                            <option value="ditolak">Tolak</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Catatan</label>
                        <textarea name="catatan_verifikasi" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm w-100">
                        <i class="bi bi-check-circle"></i> Verifikasi
                    </button>
                </form>
            </div>
        </div>
        @elseif ($umkm->verifikator)
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body">
                <small class="text-muted">
                    Diverifikasi oleh <strong>{{ $umkm->verifikator->name }}</strong><br>
                    {{ $umkm->verified_at?->format('d F Y, H:i') }}
                </small>
            </div>
        </div>
        @endif
        @endcan
    </div>

    {{-- Deskripsi & Produk --}}
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold">Tentang Usaha</h6>
                <p>{{ $umkm->deskripsi ?? 'Belum ada deskripsi.' }}</p>
            </div>
        </div>

        {{-- Produk --}}
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong><i class="bi bi-box"></i> Produk ({{ $umkm->produks->count() }})</strong>
                @if ($umkm->user_id === auth()->id() || auth()->user()->hasAnyRole(['super_admin', 'ketua_rw']))
                <a href="{{ route('umkm.produk.create', ['umkm_id' => $umkm->id]) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Produk
                </a>
                @endif
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @forelse ($umkm->produks as $p)
                    <div class="col-md-6">
                        <div class="border rounded p-2 h-100">
                            <div class="d-flex">
                                @if ($p->foto_url)
                                    <img src="{{ $p->foto_url }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 5px;" class="me-2">
                                @else
                                    <div class="d-flex align-items-center justify-content-center me-2" 
                                         style="width: 80px; height: 80px; background: #f1f5f9; border-radius: 5px;">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <strong>{{ $p->nama }}</strong>
                                    @if ($p->is_unggulan)
                                        <span class="badge bg-warning text-dark">Unggulan</span>
                                    @endif
                                    <div class="text-primary fw-bold">Rp {{ number_format($p->harga, 0, ',', '.') }} / {{ $p->satuan }}</div>
                                    @if (!$p->is_tersedia)
                                        <span class="badge bg-secondary">Habis</span>
                                    @endif
                                    <div class="mt-1">
                                        <a href="{{ route('umkm.produk.edit', $p) }}" class="btn btn-sm btn-outline-warning py-0">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('umkm.produk.destroy', $p) }}" class="d-inline"
                                              onsubmit="return confirm('Hapus produk ini?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger py-0"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-3 text-muted">
                        Belum ada produk.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
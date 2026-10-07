@extends('layouts.admin')

@section('title', 'Daftarkan UMKM')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-1">Daftarkan UMKM</h5>
    <a href="{{ route('umkm.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<div class="alert alert-info">
    <i class="bi bi-info-circle"></i>
    Setelah didaftarkan, UMKM Anda akan <strong>menunggu verifikasi</strong> dari RT/RW sebelum tampil di halaman publik.
</div>

<form method="POST" action="{{ route('umkm.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h6 class="fw-bold mb-3 text-primary">Info Usaha</h6>
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nama Usaha <span class="text-danger">*</span></label>
                    <input type="text" name="nama_usaha" class="form-control" value="{{ old('nama_usaha') }}" required
                           placeholder="Contoh: Warung Sembako Bu Siti">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select name="kategori" class="form-select" required>
                        @foreach (['makanan' => 'Makanan', 'minuman' => 'Minuman', 'jasa' => 'Jasa', 'fashion' => 'Fashion', 'kerajinan' => 'Kerajinan', 'pertanian' => 'Pertanian', 'elektronik' => 'Elektronik', 'lainnya' => 'Lainnya'] as $v => $l)
                            <option value="{{ $v }}" {{ old('kategori', 'makanan') == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi Usaha</label>
                    <textarea name="deskripsi" class="form-control" rows="3" 
                              placeholder="Jelaskan usaha Anda...">{{ old('deskripsi') }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">RT</label>
                    <select name="rt_id" class="form-select">
                        <option value="">-- Pilih RT --</option>
                        @foreach ($rts as $rt)
                            <option value="{{ $rt->id }}" {{ old('rt_id', auth()->user()->rt_id) == $rt->id ? 'selected' : '' }}>
                                {{ $rt->nama_rt }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jam Operasional</label>
                    <input type="text" name="jam_operasional" class="form-control" value="{{ old('jam_operasional') }}"
                           placeholder="Contoh: 08:00 - 21:00">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Alamat</label>
                    <input type="text" name="alamat" class="form-control" value="{{ old('alamat') }}"
                           placeholder="Jl. ... No. ...">
                </div>
            </div>

            <hr class="my-4">
            <h6 class="fw-bold mb-3 text-primary">Kontak</h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">No HP</label>
                    <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">WhatsApp</label>
                    <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp') }}"
                           placeholder="08xxxxxxxxxx">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Instagram</label>
                    <input type="text" name="instagram" class="form-control" value="{{ old('instagram') }}"
                           placeholder="@username">
                </div>
            </div>

            <hr class="my-4">
            <h6 class="fw-bold mb-3 text-primary">Foto</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Logo Usaha</label>
                    <input type="file" name="logo" class="form-control" accept="image/*">
                    <small class="text-muted">Max 2MB</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Foto Usaha</label>
                    <input type="file" name="foto_usaha" class="form-control" accept="image/*">
                    <small class="text-muted">Max 2MB</small>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('umkm.index') }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Daftarkan</button>
        </div>
    </div>
</form>

@endsection
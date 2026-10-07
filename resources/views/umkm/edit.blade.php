@extends('layouts.admin')

@section('title', 'Edit UMKM: ' . $umkm->nama_usaha)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-1">Edit UMKM</h5>
    <a href="{{ route('umkm.show', $umkm) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('umkm.update', $umkm) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h6 class="fw-bold mb-3 text-primary">Info Usaha</h6>
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nama Usaha <span class="text-danger">*</span></label>
                    <input type="text" name="nama_usaha" class="form-control" value="{{ old('nama_usaha', $umkm->nama_usaha) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kategori</label>
                    <select name="kategori" class="form-select" required>
                        @foreach (['makanan' => 'Makanan', 'minuman' => 'Minuman', 'jasa' => 'Jasa', 'fashion' => 'Fashion', 'kerajinan' => 'Kerajinan', 'pertanian' => 'Pertanian', 'elektronik' => 'Elektronik', 'lainnya' => 'Lainnya'] as $v => $l)
                            <option value="{{ $v }}" {{ old('kategori', $umkm->kategori) == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $umkm->deskripsi) }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">RT</label>
                    <select name="rt_id" class="form-select">
                        <option value="">-- Pilih RT --</option>
                        @foreach ($rts as $rt)
                            <option value="{{ $rt->id }}" {{ old('rt_id', $umkm->rt_id) == $rt->id ? 'selected' : '' }}>{{ $rt->nama_rt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jam Operasional</label>
                    <input type="text" name="jam_operasional" class="form-control" value="{{ old('jam_operasional', $umkm->jam_operasional) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Alamat</label>
                    <input type="text" name="alamat" class="form-control" value="{{ old('alamat', $umkm->alamat) }}">
                </div>
            </div>

            <hr class="my-4">
            <h6 class="fw-bold mb-3 text-primary">Kontak</h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">No HP</label>
                    <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $umkm->no_hp) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">WhatsApp</label>
                    <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp', $umkm->whatsapp) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $umkm->email) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Instagram</label>
                    <input type="text" name="instagram" class="form-control" value="{{ old('instagram', $umkm->instagram) }}">
                </div>
            </div>

            <hr class="my-4">
            <h6 class="fw-bold mb-3 text-primary">Foto</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Logo</label>
                    <input type="file" name="logo" class="form-control" accept="image/*">
                    @if ($umkm->logo_url)
                        <img src="{{ $umkm->logo_url }}" style="max-width: 100px; border-radius: 5px;" class="mt-2">
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">Foto Usaha</label>
                    <input type="file" name="foto_usaha" class="form-control" accept="image/*">
                    @if ($umkm->foto_usaha_url)
                        <img src="{{ $umkm->foto_usaha_url }}" style="max-width: 150px; border-radius: 5px;" class="mt-2">
                    @endif
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('umkm.show', $umkm) }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Update</button>
        </div>
    </div>
</form>

@endsection
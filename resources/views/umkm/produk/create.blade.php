@extends('layouts.admin')

@section('title', 'Tambah Produk UMKM')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Tambah Produk</h5>
        <small class="text-muted">{{ $umkm->nama_usaha }}</small>
    </div>
    <a href="{{ route('umkm.show', $umkm) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('umkm.produk.store') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="umkm_id" value="{{ $umkm->id }}">

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Satuan <span class="text-danger">*</span></label>
                    <input type="text" name="satuan" class="form-control" value="{{ old('satuan', 'pcs') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="harga" class="form-control" value="{{ old('harga') }}" min="0" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Urutan</label>
                    <input type="number" name="urutan" class="form-control" value="{{ old('urutan', 0) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Foto Produk</label>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi Produk</label>
                    <textarea name="deskripsi" class="form-control" rows="2">{{ old('deskripsi') }}</textarea>
                </div>
                <div class="col-md-6">
                    <div class="form-check">
                        <input type="checkbox" name="is_tersedia" value="1" class="form-check-input" id="is_tersedia"
                               {{ old('is_tersedia', true) ? 'checked' : '' }}>
                        <label for="is_tersedia" class="form-check-label">Produk Tersedia</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check">
                        <input type="checkbox" name="is_unggulan" value="1" class="form-check-input" id="is_unggulan"
                               {{ old('is_unggulan') ? 'checked' : '' }}>
                        <label for="is_unggulan" class="form-check-label">Produk Unggulan</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('umkm.show', $umkm) }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Simpan</button>
        </div>
    </div>
</form>

@endsection
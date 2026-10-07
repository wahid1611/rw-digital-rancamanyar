@extends('layouts.admin')

@section('title', 'Edit Produk: ' . $produk->nama)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-1">Edit Produk</h5>
    <a href="{{ route('umkm.show', $produk->umkm_id) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('umkm.produk.update', $produk) }}" enctype="multipart/form-data">
    @csrf @method('PUT')

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $produk->nama) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Satuan <span class="text-danger">*</span></label>
                    <input type="text" name="satuan" class="form-control" value="{{ old('satuan', $produk->satuan) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="harga" class="form-control" value="{{ old('harga', $produk->harga) }}" min="0" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Urutan</label>
                    <input type="number" name="urutan" class="form-control" value="{{ old('urutan', $produk->urutan) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Foto</label>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                    @if ($produk->foto_url)
                        <img src="{{ $produk->foto_url }}" style="max-width: 100px; border-radius: 5px;" class="mt-2">
                    @endif
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="2">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                </div>
                <div class="col-md-6">
                    <div class="form-check">
                        <input type="checkbox" name="is_tersedia" value="1" class="form-check-input" id="is_tersedia"
                               {{ old('is_tersedia', $produk->is_tersedia) ? 'checked' : '' }}>
                        <label for="is_tersedia" class="form-check-label">Tersedia</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check">
                        <input type="checkbox" name="is_unggulan" value="1" class="form-check-input" id="is_unggulan"
                               {{ old('is_unggulan', $produk->is_unggulan) ? 'checked' : '' }}>
                        <label for="is_unggulan" class="form-check-label">Unggulan</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('umkm.show', $produk->umkm_id) }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Update</button>
        </div>
    </div>
</form>

@endsection
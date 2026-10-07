@extends('layouts.admin')

@section('title', 'Catat Uang Keluar')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Catat Uang Keluar</h5>
        <small class="text-muted">Catat pengeluaran Kas RW</small>
    </div>
    <a href="{{ route('keuangan.rw.dashboard') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('keuangan.rw.keluar.store') }}" enctype="multipart/form-data">
    @csrf
    
    <div class="card border-0 shadow-sm">
        <div class="card-body p-3">
            
            {{-- Uang ini untuk apa --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Uang ini untuk apa?
                </label>
                <input type="text" name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" 
                       value="{{ old('deskripsi') }}" required
                       placeholder="Contoh: Beli lampu jalan 5 pcs">
                @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Kategori --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Kategori
                </label>
                <select name="kategori" class="form-select" required>
                    <option value="operasional" {{ old('kategori') == 'operasional' ? 'selected' : '' }}>Operasional</option>
                    <option value="perbaikan" {{ old('kategori') == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                    <option value="kegiatan" {{ old('kategori') == 'kegiatan' ? 'selected' : '' }}>Kegiatan</option>
                    <option value="sosial" {{ old('kategori') == 'sosial' ? 'selected' : '' }}>Sosial</option>
                    <option value="lainnya" {{ old('kategori') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            {{-- Berapa uangnya --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Berapa uangnya?
                </label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="nominal" class="form-control @error('nominal') is-invalid @enderror" 
                           value="{{ old('nominal') }}" min="1" required
                           placeholder="500000" style="font-weight: 600;">
                </div>
                @error('nominal') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Tanggal --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Tanggal keluar?
                </label>
                <input type="date" name="tgl_transaksi" class="form-control @error('tgl_transaksi') is-invalid @enderror" 
                       value="{{ old('tgl_transaksi', now()->format('Y-m-d')) }}" required>
                @error('tgl_transaksi') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Bukti --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Foto Nota (opsional)
                </label>
                <input type="file" name="bukti" class="form-control" accept="image/*">
                <small class="text-muted">Foto nota / kwitansi, max 2MB</small>
            </div>

        </div>
        <div class="card-footer bg-white p-3 d-flex justify-content-between">
            <a href="{{ route('keuangan.rw.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Batal
            </a>
            <button type="submit" class="btn btn-danger px-4"
                    onclick="return confirm('Yakin simpan uang keluar ini?')">
                <i class="bi bi-check-circle"></i> Simpan Uang Keluar
            </button>
        </div>
    </div>
</form>

@endsection
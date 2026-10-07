@extends('layouts.admin')

@section('title', 'Catat Uang Masuk ' . $rt->nama_rt)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Catat Uang Masuk</h5>
        <small class="text-muted">Catat uang yang masuk ke Kas {{ $rt->nama_rt }}</small>
    </div>
    <a href="{{ route('keuangan.rt.dashboard') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('keuangan.rt.masuk.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="card border-0 shadow-sm">
        <div class="card-body p-3">

            {{-- Uang masuk dari siapa --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Uang masuk dari siapa?
                </label>
                <select name="keluarga_id" class="form-select @error('keluarga_id') is-invalid @enderror">
                    <option value="">-- Pilih Warga / KK {{ $rt->nama_rt }} --</option>
                    @foreach ($keluargas as $kk)
                        <option value="{{ $kk->id }}" {{ old('keluarga_id') == $kk->id ? 'selected' : '' }}>
                            {{ $kk->kepala_keluarga_nama }} ({{ $kk->no_kk }})
                        </option>
                    @endforeach
                </select>
                @error('keluarga_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Untuk iuran apa --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Untuk iuran apa?
                </label>
                <input type="text" name="deskripsi_iuran" class="form-control"
                       value="{{ old('deskripsi_iuran') }}"
                       placeholder="Contoh: Iuran RT, Iuran Sampah, Kas RT"
                       list="daftar-iuran">
                <datalist id="daftar-iuran">
                    @foreach ($iurans as $i)
                        <option value="{{ $i->nama }}">
                    @endforeach
                </datalist>
                <small class="text-muted">Ketik bebas, atau pilih dari saran</small>
            </div>

            {{-- Kategori --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Kategori
                </label>
                <select name="kategori" class="form-select" required>
                    <option value="iuran" {{ old('kategori') == 'iuran' ? 'selected' : '' }}>Iuran Warga</option>
                    <option value="sumbangan" {{ old('kategori') == 'sumbangan' ? 'selected' : '' }}>Sumbangan</option>
                    <option value="bantuan" {{ old('kategori') == 'bantuan' ? 'selected' : '' }}>Bantuan</option>
                    <option value="denda" {{ old('kategori') == 'denda' ? 'selected' : '' }}>Denda</option>
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
                           placeholder="50000" style="font-weight: 600;">
                </div>
                @error('nominal') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Tanggal --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Tanggal terima?
                </label>
                <input type="date" name="tgl_transaksi" class="form-control @error('tgl_transaksi') is-invalid @enderror"
                       value="{{ old('tgl_transaksi', now()->format('Y-m-d')) }}" required>
                @error('tgl_transaksi') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Deskripsi --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Keterangan (opsional)
                </label>
                <textarea name="deskripsi" class="form-control" rows="2"
                          placeholder="Contoh: Budi bayar iuran RT bulan Oktober">{{ old('deskripsi') }}</textarea>
            </div>

            {{-- Bukti --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Foto Bukti (opsional)
                </label>
                <input type="file" name="bukti" class="form-control" accept="image/*">
                <small class="text-muted">Foto bukti transfer / kwitansi, max 2MB</small>
            </div>

        </div>
        <div class="card-footer bg-white p-3 d-flex justify-content-between">
            <a href="{{ route('keuangan.rt.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Batal
            </a>
            <button type="submit" class="btn btn-success px-4"
                    onclick="return confirm('Yakin simpan uang masuk ini?')">
                <i class="bi bi-check-circle"></i> Simpan Uang Masuk
            </button>
        </div>
    </div>
</form>

@endsection
@extends('layouts.admin')

@section('title', 'Buat Program Bansos')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-1">Buat Program Bantuan Sosial</h5>
    <a href="{{ route('bansos.program.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('bansos.program.store') }}">
    @csrf
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nama Program <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required
                           placeholder="Contoh: BLT Desa 2026">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select name="kategori" class="form-select" required>
                        @foreach (['blt' => 'BLT (Bantuan Langsung Tunai)', 'pkh' => 'PKH (Program Keluarga Harapan)', 'bpnt' => 'BPNT (Bantuan Pangan Non Tunai)', 'sembako' => 'Sembako', 'kesehatan' => 'Kesehatan', 'pendidikan' => 'Pendidikan', 'bencana' => 'Bantuan Bencana', 'lainnya' => 'Lainnya'] as $v => $l)
                            <option value="{{ $v }}" {{ old('kategori', 'blt') == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="2">{{ old('deskripsi') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Sumber Dana</label>
                    <input type="text" name="sumber" class="form-control" value="{{ old('sumber') }}"
                           placeholder="Pemerintah / Donatur / RW">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kriteria Penerima</label>
                    <textarea name="kriteria" class="form-control" rows="2" 
                              placeholder="Contoh: Keluarga kurang mampu">{{ old('kriteria') }}</textarea>
                </div>
            </div>

            <hr class="my-4">
            <h6 class="fw-bold mb-3 text-primary">Nilai Bantuan</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Jenis Bantuan <span class="text-danger">*</span></label>
                    <select name="jenis_bantuan" class="form-select" required>
                        <option value="uang" {{ old('jenis_bantuan', 'uang') == 'uang' ? 'selected' : '' }}>Uang</option>
                        <option value="barang" {{ old('jenis_bantuan') == 'barang' ? 'selected' : '' }}>Barang</option>
                        <option value="jasa" {{ old('jenis_bantuan') == 'jasa' ? 'selected' : '' }}>Jasa</option>
                        <option value="campuran" {{ old('jenis_bantuan') == 'campuran' ? 'selected' : '' }}>Campuran</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nominal (Rp)</label>
                    <input type="number" name="nominal" class="form-control" value="{{ old('nominal') }}" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Satuan Bantuan</label>
                    <input type="text" name="satuan_bantuan" class="form-control" value="{{ old('satuan_bantuan') }}"
                           placeholder="per bulan / sekali">
                </div>
            </div>

            <hr class="my-4">
            <h6 class="fw-bold mb-3 text-primary">Periode</h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Periode</label>
                    <input type="text" name="periode" class="form-control" value="{{ old('periode') }}"
                           placeholder="Bulanan 2026">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="selesai" {{ old('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="batal" {{ old('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('bansos.program.index') }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Simpan Program</button>
        </div>
    </div>
</form>

@endsection
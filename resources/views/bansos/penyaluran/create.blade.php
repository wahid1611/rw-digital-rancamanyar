@extends('layouts.admin')

@section('title', 'Catat Penyaluran Bansos')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-1">Catat Penyaluran Bansos</h5>
    <a href="{{ route('bansos.penyaluran.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('bansos.penyaluran.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Penerima <span class="text-danger">*</span></label>
                    <select name="penerima_id" class="form-select" required>
                        <option value="">-- Pilih Penerima --</option>
                        @foreach ($penerimas as $p)
                            <option value="{{ $p->id }}" {{ old('penerima_id', $selectedPenerima->id ?? '') == $p->id ? 'selected' : '' }}>
                                {{ $p->nama_penerima }} — {{ $p->program->nama ?? '-' }} ({{ $p->rt->nama_rt ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Hanya penerima dengan status "Layak" yang muncul</small>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Periode</label>
                    <input type="text" name="periode" class="form-control" value="{{ old('periode') }}" 
                           placeholder="Oktober 2026">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Nominal (Rp)</label>
                    <input type="number" name="nominal" class="form-control" value="{{ old('nominal') }}" min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jenis Bantuan</label>
                    <input type="text" name="jenis_bantuan" class="form-control" value="{{ old('jenis_bantuan') }}"
                           placeholder="uang / barang / jasa">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="disalurkan" {{ old('status', 'disalurkan') == 'disalurkan' ? 'selected' : '' }}>Disalurkan</option>
                        <option value="dijadwalkan" {{ old('status') == 'dijadwalkan' ? 'selected' : '' }}>Dijadwalkan</option>
                        <option value="dibatalkan" {{ old('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Foto Bukti</label>
                    <input type="file" name="foto_bukti" class="form-control" accept="image/*">
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi Barang (jika bantuan barang)</label>
                    <input type="text" name="deskripsi_barang" class="form-control" value="{{ old('deskripsi_barang') }}"
                           placeholder="Contoh: Beras 5 kg, Minyak 2 liter">
                </div>
                <div class="col-12">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="2">{{ old('catatan') }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('bansos.penyaluran.index') }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Simpan Penyaluran</button>
        </div>
    </div>
</form>

@endsection
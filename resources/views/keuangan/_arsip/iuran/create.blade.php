@extends('layouts.admin')

@section('title', 'Tambah Jenis Iuran')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-1">Tambah Jenis Iuran</h5>
    <a href="{{ route('keuangan.iuran.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('keuangan.iuran.store') }}">
    @csrf
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nama Iuran <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required
                           placeholder="Contoh: Iuran Bulanan">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select name="kategori" class="form-select" required>
                        @foreach (['bulanan' => 'Iuran Bulanan', 'keamanan' => 'Keamanan', 'kebersihan' => 'Kebersihan', 'kesehatan' => 'Kesehatan', 'sosial' => 'Sosial', 'lainnya' => 'Lainnya'] as $v => $l)
                            <option value="{{ $v }}" {{ old('kategori', 'bulanan') == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nominal Default (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="nominal_default" class="form-control" 
                           value="{{ old('nominal_default', 50000) }}" min="0" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Periode <span class="text-danger">*</span></label>
                    <select name="periode" class="form-select" required>
                        @foreach (['bulanan' => 'Bulanan', 'triwulan' => 'Triwulan (3 bulan)', 'tahunan' => 'Tahunan', 'sekali' => 'Sekali'] as $v => $l)
                            <option value="{{ $v }}" {{ old('periode', 'bulanan') == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jatuh Tempo (tgl) <span class="text-danger">*</span></label>
                    <input type="number" name="jatuh_tempo_tgl" class="form-control" 
                           value="{{ old('jatuh_tempo_tgl', 10) }}" min="1" max="28" required>
                    <small class="text-muted">Tanggal berapa tiap periode</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Berlaku untuk</label>
                    <select name="rt_id" class="form-select">
                        <option value="">Semua RT</option>
                        @foreach ($rts as $rt)
                            <option value="{{ $rt->id }}" {{ old('rt_id') == $rt->id ? 'selected' : '' }}>
                                Khusus {{ $rt->nama_rt }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check me-3">
                        <input type="checkbox" name="per_kk" value="1" class="form-check-input" id="per_kk"
                               {{ old('per_kk', true) ? 'checked' : '' }}>
                        <label for="per_kk" class="form-check-label">Per KK</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label for="is_active" class="form-check-label">Aktif</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('keuangan.iuran.index') }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Simpan</button>
        </div>
    </div>
</form>

@endsection
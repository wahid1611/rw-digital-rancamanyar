@extends('layouts.admin')

@section('title', 'Catat Tamu Baru')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-1">Catat Tamu Baru</h5>
    <a href="{{ route('tamu.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('tamu.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h6 class="fw-bold mb-3 text-primary">👤 Data Tamu</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Tamu <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">No HP</label>
                    <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}" 
                           placeholder="08xxxxxxxxxx">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Instansi (opsional)</label>
                    <input type="text" name="instansi" class="form-control" value="{{ old('instansi') }}" 
                           placeholder="PT / Komunitas">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jenis Identitas</label>
                    <select name="jenis_identitas" class="form-select">
                        <option value="">-- Pilih --</option>
                        <option value="KTP" {{ old('jenis_identitas') == 'KTP' ? 'selected' : '' }}>KTP</option>
                        <option value="SIM" {{ old('jenis_identitas') == 'SIM' ? 'selected' : '' }}>SIM</option>
                        <option value="Paspor" {{ old('jenis_identitas') == 'Paspor' ? 'selected' : '' }}>Paspor</option>
                        <option value="Lainnya" {{ old('jenis_identitas') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">No Identitas</label>
                    <input type="text" name="no_identitas" class="form-control" value="{{ old('no_identitas') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Alamat Asal</label>
                    <input type="text" name="alamat_asal" class="form-control" value="{{ old('alamat_asal') }}"
                           placeholder="Jl. ... No. ...">
                </div>
            </div>

            <hr class="my-4">
            <h6 class="fw-bold mb-3 text-primary">🎯 Tujuan Kunjungan</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tujuan <span class="text-danger">*</span></label>
                    <select name="tujuan_tipe" id="tujuan_tipe" class="form-select" required onchange="updateTujuan()">
                        <option value="warga" {{ old('tujuan_tipe', 'warga') == 'warga' ? 'selected' : '' }}>Ke Warga</option>
                        <option value="rt" {{ old('tujuan_tipe') == 'rt' ? 'selected' : '' }}>Ke RT</option>
                        <option value="rw" {{ old('tujuan_tipe') == 'rw' ? 'selected' : '' }}>Ke RW</option>
                        <option value="umum" {{ old('tujuan_tipe') == 'umum' ? 'selected' : '' }}>Umum / Fasilitas RW</option>
                        <option value="lainnya" {{ old('tujuan_tipe') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div class="col-md-4" id="wrap-warga">
                    <label class="form-label">Nama Warga</label>
                    <select name="tujuan_user_id" class="form-select">
                        <option value="">-- Pilih Warga --</option>
                        @foreach ($wargas as $w)
                            <option value="{{ $w->id }}" {{ old('tujuan_user_id') == $w->id ? 'selected' : '' }}>
                                {{ $w->name }} — {{ $w->rt->nama_rt ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4" id="wrap-rt" style="display: none;">
                    <label class="form-label">Pilih RT</label>
                    <select name="tujuan_rt_id" class="form-select">
                        <option value="">-- Pilih RT --</option>
                        @foreach ($rts as $rt)
                            <option value="{{ $rt->id }}" {{ old('tujuan_rt_id') == $rt->id ? 'selected' : '' }}>
                                {{ $rt->nama_rt }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Keperluan <span class="text-danger">*</span></label>
                    <textarea name="keperluan" class="form-control" rows="3" required
                              placeholder="Jelaskan keperluan kunjungan...">{{ old('keperluan') }}</textarea>
                </div>
            </div>

            <hr class="my-4">
            <h6 class="fw-bold mb-3 text-primary">🚗 Kendaraan (opsional)</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Jenis Kendaraan</label>
                    <input type="text" name="jenis_kendaraan" class="form-control" value="{{ old('jenis_kendaraan') }}"
                           placeholder="Motor / Mobil / Umum">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Plat Nomor</label>
                    <input type="text" name="plat_nomor" class="form-control" value="{{ old('plat_nomor') }}"
                           placeholder="Contoh: T 1234 ABC">
                </div>
            </div>

            <hr class="my-4">
            <h6 class="fw-bold mb-3 text-primary">📷 Foto</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Foto Tamu</label>
                    <input type="file" name="foto_tamu" class="form-control" accept="image/*" capture="user">
                    <small class="text-muted">Max 2MB</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Foto KTP/Identitas</label>
                    <input type="file" name="foto_ktp" class="form-control" accept="image/*">
                    <small class="text-muted">Max 2MB</small>
                </div>
                <div class="col-12">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="2">{{ old('catatan') }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('tamu.index') }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Simpan</button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    function updateTujuan() {
        const tipe = document.getElementById('tujuan_tipe').value;
        const wrapWarga = document.getElementById('wrap-warga');
        const wrapRt = document.getElementById('wrap-rt');

        if (tipe === 'warga') {
            wrapWarga.style.display = 'block';
            wrapRt.style.display = 'none';
        } else if (tipe === 'rt') {
            wrapWarga.style.display = 'none';
            wrapRt.style.display = 'block';
        } else {
            wrapWarga.style.display = 'none';
            wrapRt.style.display = 'none';
        }
    }
    document.addEventListener('DOMContentLoaded', updateTujuan);
</script>
@endpush

@endsection
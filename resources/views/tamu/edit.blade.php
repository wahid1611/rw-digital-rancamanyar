@extends('layouts.admin')

@section('title', 'Edit Tamu: ' . $tamu->nama)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-1">Edit Tamu</h5>
    <a href="{{ route('tamu.show', $tamu) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('tamu.update', $tamu) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h6 class="fw-bold mb-3 text-primary">👤 Data Tamu</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Tamu <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $tamu->nama) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">No HP</label>
                    <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $tamu->no_hp) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Instansi</label>
                    <input type="text" name="instansi" class="form-control" value="{{ old('instansi', $tamu->instansi) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jenis Identitas</label>
                    <select name="jenis_identitas" class="form-select">
                        <option value="">-- Pilih --</option>
                        @foreach (['KTP', 'SIM', 'Paspor', 'Lainnya'] as $j)
                            <option value="{{ $j }}" {{ old('jenis_identitas', $tamu->jenis_identitas) == $j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">No Identitas</label>
                    <input type="text" name="no_identitas" class="form-control" value="{{ old('no_identitas', $tamu->no_identitas) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Alamat Asal</label>
                    <input type="text" name="alamat_asal" class="form-control" value="{{ old('alamat_asal', $tamu->alamat_asal) }}">
                </div>
            </div>

            <hr class="my-4">
            <h6 class="fw-bold mb-3 text-primary">🎯 Tujuan</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tujuan <span class="text-danger">*</span></label>
                    <select name="tujuan_tipe" class="form-select" required>
                        @foreach (['warga' => 'Ke Warga', 'rt' => 'Ke RT', 'rw' => 'Ke RW', 'umum' => 'Umum', 'lainnya' => 'Lainnya'] as $v => $l)
                            <option value="{{ $v }}" {{ old('tujuan_tipe', $tamu->tujuan_tipe) == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nama Warga</label>
                    <select name="tujuan_user_id" class="form-select">
                        <option value="">-- Pilih Warga --</option>
                        @foreach ($wargas as $w)
                            <option value="{{ $w->id }}" {{ old('tujuan_user_id', $tamu->tujuan_user_id) == $w->id ? 'selected' : '' }}>
                                {{ $w->name }} — {{ $w->rt->nama_rt ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">RT</label>
                    <select name="tujuan_rt_id" class="form-select">
                        <option value="">-- Pilih RT --</option>
                        @foreach ($rts as $rt)
                            <option value="{{ $rt->id }}" {{ old('tujuan_rt_id', $tamu->tujuan_rt_id) == $rt->id ? 'selected' : '' }}>
                                {{ $rt->nama_rt }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Keperluan <span class="text-danger">*</span></label>
                    <textarea name="keperluan" class="form-control" rows="3" required>{{ old('keperluan', $tamu->keperluan) }}</textarea>
                </div>
            </div>

            <hr class="my-4">
            <h6 class="fw-bold mb-3 text-primary">🚗 Kendaraan</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Jenis Kendaraan</label>
                    <input type="text" name="jenis_kendaraan" class="form-control" value="{{ old('jenis_kendaraan', $tamu->jenis_kendaraan) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Plat Nomor</label>
                    <input type="text" name="plat_nomor" class="form-control" value="{{ old('plat_nomor', $tamu->plat_nomor) }}">
                </div>
            </div>

            <hr class="my-4">
            <h6 class="fw-bold mb-3 text-primary">📷 Foto</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Foto Tamu</label>
                    <input type="file" name="foto_tamu" class="form-control" accept="image/*">
                    @if ($tamu->foto_tamu_url)
                        <img src="{{ $tamu->foto_tamu_url }}" style="max-width: 150px; border-radius: 5px;" class="mt-2">
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">Foto KTP</label>
                    <input type="file" name="foto_ktp" class="form-control" accept="image/*">
                    @if ($tamu->foto_ktp_url)
                        <img src="{{ $tamu->foto_ktp_url }}" style="max-width: 150px; border-radius: 5px;" class="mt-2">
                    @endif
                </div>
                <div class="col-12">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="2">{{ old('catatan', $tamu->catatan) }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('tamu.show', $tamu) }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Update</button>
        </div>
    </div>
</form>

@endsection
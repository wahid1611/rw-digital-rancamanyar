@extends('layouts.admin')

@section('title', 'Buat Jadwal Posyandu')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-1">Buat Jadwal Posyandu</h5>
    <a href="{{ route('posyandu.jadwal.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('posyandu.jadwal.store') }}">
    @csrf
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nama Jadwal <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required
                           placeholder="Contoh: Posyandu Balita Oktober 2026">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jenis <span class="text-danger">*</span></label>
                    <select name="jenis" class="form-select" required>
                        <option value="balita" {{ old('jenis') == 'balita' ? 'selected' : '' }}>Balita</option>
                        <option value="lansia" {{ old('jenis') == 'lansia' ? 'selected' : '' }}>Lansia</option>
                        <option value="umum" {{ old('jenis', 'umum') == 'umum' ? 'selected' : '' }}>Umum</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jam Mulai</label>
                    <input type="time" name="jam_mulai" class="form-control" value="{{ old('jam_mulai', '08:00') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jam Selesai</label>
                    <input type="time" name="jam_selesai" class="form-control" value="{{ old('jam_selesai', '11:00') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Lokasi</label>
                    <input type="text" name="lokasi" class="form-control" value="{{ old('lokasi') }}"
                           placeholder="Contoh: Balai RW 07">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="rencana" {{ old('status', 'rencana') == 'rencana' ? 'selected' : '' }}>Rencana</option>
                        <option value="berlangsung" {{ old('status') == 'berlangsung' ? 'selected' : '' }}>Berlangsung</option>
                        <option value="selesai" {{ old('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="batal" {{ old('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('posyandu.jadwal.index') }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Simpan</button>
        </div>
    </div>
</form>

@endsection
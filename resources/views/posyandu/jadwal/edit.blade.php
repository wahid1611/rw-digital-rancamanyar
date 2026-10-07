@extends('layouts.admin')

@section('title', 'Edit Jadwal: ' . $jadwal->nama)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-1">Edit Jadwal Posyandu</h5>
    <a href="{{ route('posyandu.jadwal.show', $jadwal) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('posyandu.jadwal.update', $jadwal) }}">
    @csrf @method('PUT')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nama Jadwal</label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $jadwal->nama) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jenis</label>
                    <select name="jenis" class="form-select" required>
                        @foreach (['balita' => 'Balita', 'lansia' => 'Lansia', 'umum' => 'Umum'] as $v => $l)
                            <option value="{{ $v }}" {{ old('jenis', $jadwal->jenis) == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $jadwal->tanggal->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jam Mulai</label>
                    <input type="time" name="jam_mulai" class="form-control" value="{{ old('jam_mulai', $jadwal->jam_mulai) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jam Selesai</label>
                    <input type="time" name="jam_selesai" class="form-control" value="{{ old('jam_selesai', $jadwal->jam_selesai) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Lokasi</label>
                    <input type="text" name="lokasi" class="form-control" value="{{ old('lokasi', $jadwal->lokasi) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        @foreach (['rencana' => 'Rencana', 'berlangsung' => 'Berlangsung', 'selesai' => 'Selesai', 'batal' => 'Batal'] as $v => $l)
                            <option value="{{ $v }}" {{ old('status', $jadwal->status) == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $jadwal->keterangan) }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('posyandu.jadwal.show', $jadwal) }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Update</button>
        </div>
    </div>
</form>

@endsection
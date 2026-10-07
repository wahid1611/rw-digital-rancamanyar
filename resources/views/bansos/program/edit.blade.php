@extends('layouts.admin')

@section('title', 'Edit Program: ' . $program->nama)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-1">Edit Program Bansos</h5>
    <a href="{{ route('bansos.program.show', $program) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('bansos.program.update', $program) }}">
    @csrf @method('PUT')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nama Program <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $program->nama) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select name="kategori" class="form-select" required>
                        @foreach (['blt' => 'BLT', 'pkh' => 'PKH', 'bpnt' => 'BPNT', 'sembako' => 'Sembako', 'kesehatan' => 'Kesehatan', 'pendidikan' => 'Pendidikan', 'bencana' => 'Bencana', 'lainnya' => 'Lainnya'] as $v => $l)
                            <option value="{{ $v }}" {{ old('kategori', $program->kategori) == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="2">{{ old('deskripsi', $program->deskripsi) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Sumber</label>
                    <input type="text" name="sumber" class="form-control" value="{{ old('sumber', $program->sumber) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kriteria</label>
                    <textarea name="kriteria" class="form-control" rows="2">{{ old('kriteria', $program->kriteria) }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jenis Bantuan</label>
                    <select name="jenis_bantuan" class="form-select" required>
                        @foreach (['uang' => 'Uang', 'barang' => 'Barang', 'jasa' => 'Jasa', 'campuran' => 'Campuran'] as $v => $l)
                            <option value="{{ $v }}" {{ old('jenis_bantuan', $program->jenis_bantuan) == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nominal</label>
                    <input type="number" name="nominal" class="form-control" value="{{ old('nominal', $program->nominal) }}" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Satuan</label>
                    <input type="text" name="satuan_bantuan" class="form-control" value="{{ old('satuan_bantuan', $program->satuan_bantuan) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai', $program->tanggal_mulai->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai', $program->tanggal_selesai?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Periode</label>
                    <input type="text" name="periode" class="form-control" value="{{ old('periode', $program->periode) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        @foreach (['draft' => 'Draft', 'aktif' => 'Aktif', 'selesai' => 'Selesai', 'batal' => 'Batal'] as $v => $l)
                            <option value="{{ $v }}" {{ old('status', $program->status) == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('bansos.program.show', $program) }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Update</button>
        </div>
    </div>
</form>

@endsection
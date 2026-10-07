@extends('layouts.admin')

@section('title', 'Edit Tagihan: ' . $tagihan->kode_tagihan)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-1">Edit Tagihan</h5>
    <a href="{{ route('keuangan.tagihan.show', $tagihan) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('keuangan.tagihan.update', $tagihan) }}">
    @csrf @method('PUT')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="alert alert-secondary">
                <strong>{{ $tagihan->keluarga->kepala_keluarga_nama ?? '-' }}</strong> — 
                {{ $tagihan->iuran->nama }} ({{ $tagihan->periode }})
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Nominal <span class="text-danger">*</span></label>
                    <input type="number" name="nominal" class="form-control" 
                           value="{{ old('nominal', $tagihan->nominal) }}" required min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jatuh Tempo <span class="text-danger">*</span></label>
                    <input type="date" name="jatuh_tempo" class="form-control" 
                           value="{{ old('jatuh_tempo', $tagihan->jatuh_tempo->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        @foreach (['belum_bayar' => 'Belum Bayar', 'sebagian' => 'Sebagian', 'lunas' => 'Lunas', 'telat' => 'Telat', 'batal' => 'Batal'] as $v => $l)
                            <option value="{{ $v }}" {{ old('status', $tagihan->status) == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $tagihan->keterangan) }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('keuangan.tagihan.show', $tagihan) }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Update</button>
        </div>
    </div>
</form>

@endsection
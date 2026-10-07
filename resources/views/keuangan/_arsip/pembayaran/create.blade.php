@extends('layouts.admin')

@section('title', 'Catat Pembayaran')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-1">Catat Pembayaran</h5>
    <a href="{{ route('keuangan.pembayaran.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('keuangan.pembayaran.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Pilih Tagihan <span class="text-danger">*</span></label>
                    <select name="tagihan_id" class="form-select" required>
                        <option value="">-- Pilih Tagihan --</option>
                        @foreach ($tagihans as $t)
                            <option value="{{ $t->id }}" {{ old('tagihan_id', $selectedTagihan->id ?? '') == $t->id ? 'selected' : '' }}>
                                {{ $t->kode_tagihan }} — {{ $t->keluarga->kepala_keluarga_nama ?? '-' }} — 
                                {{ $t->iuran->nama ?? '-' }} ({{ $t->periode }}) — 
                                Tunggakan: Rp {{ number_format($t->tunggakan, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nominal Bayar <span class="text-danger">*</span></label>
                    <input type="number" name="nominal" class="form-control" 
                           value="{{ old('nominal') }}" min="1" required
                           placeholder="Contoh: 50000">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Metode <span class="text-danger">*</span></label>
                    <select name="metode" class="form-select" required>
                        @foreach (['tunai' => 'Tunai', 'transfer' => 'Transfer', 'qris' => 'QRIS', 'ewallet' => ' E-Wallet'] as $v => $l)
                            <option value="{{ $v }}" {{ old('metode', 'tunai') == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Bayar <span class="text-danger">*</span></label>
                    <input type="date" name="tgl_bayar" class="form-control" 
                           value="{{ old('tgl_bayar', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">No Referensi (untuk transfer)</label>
                    <input type="text" name="no_referensi" class="form-control" value="{{ old('no_referensi') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Bukti Transfer (opsional)</label>
                    <input type="file" name="bukti" class="form-control" accept="image/*">
                </div>
                <div class="col-12">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="2">{{ old('catatan') }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('keuangan.pembayaran.index') }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Simpan Pembayaran</button>
        </div>
    </div>
</form>

@endsection
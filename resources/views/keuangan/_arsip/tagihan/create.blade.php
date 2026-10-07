@extends('layouts.admin')

@section('title', 'Generate Tagihan')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-1">Generate Tagihan Massal</h5>
    <a href="{{ route('keuangan.tagihan.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<div class="alert alert-info">
    <i class="bi bi-info-circle"></i>
    Tagihan akan dibuat untuk <strong>semua KK aktif</strong> sesuai iuran yang dipilih. Kalau KK sudah punya tagihan di periode yang sama, akan dilewati (tidak dobel).
</div>

<form method="POST" action="{{ route('keuangan.tagihan.store') }}">
    @csrf
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label">Pilih Iuran <span class="text-danger">*</span></label>
                    <select name="iuran_id" class="form-select" required>
                        <option value="">-- Pilih Iuran --</option>
                        @foreach ($iurans as $iu)
                            <option value="{{ $iu->id }}" {{ request('iuran_id') == $iu->id ? 'selected' : '' }}>
                                {{ $iu->nama }} — Rp {{ number_format($iu->nominal_default, 0, ',', '.') }} ({{ $iu->periode_label }})
                                @if ($iu->rt) - Khusus {{ $iu->rt->nama_rt }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Periode <span class="text-danger">*</span></label>
                    <input type="text" name="periode" class="form-control" 
                           value="{{ old('periode', now()->format('Y-m')) }}" required
                           placeholder="Contoh: 2026-10">
                    <small class="text-muted">Format: YYYY-MM (mis. 2026-10 untuk Oktober 2026)</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jatuh Tempo <span class="text-danger">*</span></label>
                    <input type="date" name="jatuh_tempo" class="form-control" 
                           value="{{ old('jatuh_tempo', now()->addDays(10)->format('Y-m-d')) }}" required>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('keuangan.tagihan.index') }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary" onclick="return confirm('Yakin generate tagihan?')">
                <i class="bi bi-magic"></i> Generate Tagihan
            </button>
        </div>
    </div>
</form>

@endsection
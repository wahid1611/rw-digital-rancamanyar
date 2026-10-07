@extends('layouts.admin')

@section('title', 'Tambah Penerima Bansos')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-1">Tambah Penerima Bansos</h5>
    <a href="{{ route('bansos.penerima.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('bansos.penerima.store') }}">
    @csrf
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Program Bansos <span class="text-danger">*</span></label>
                    <select name="program_id" class="form-select" required>
                        <option value="">-- Pilih Program --</option>
                        @foreach ($programs as $p)
                            <option value="{{ $p->id }}" {{ old('program_id', $selectedProgram->id ?? '') == $p->id ? 'selected' : '' }}>
                                {{ $p->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Warga Penerima <span class="text-danger">*</span></label>
                    <select name="warga_id" class="form-select" required>
                        <option value="">-- Pilih Warga --</option>
                        @if (isset($wargas))
                            @foreach ($wargas as $w)
                                <option value="{{ $w->id }}" {{ old('warga_id') == $w->id ? 'selected' : '' }}>
                                    {{ $w->nik }} — {{ $w->nama }} ({{ $w->rt->nama_rt ?? '-' }})
                                </option>
                            @endforeach
                        @else
                            @foreach (\App\Models\Warga::with('rt')->where('status_hidup', 'hidup')->orderBy('nama')->get() as $w)
                                <option value="{{ $w->id }}" {{ old('warga_id') == $w->id ? 'selected' : '' }}>
                                    {{ $w->nik }} — {{ $w->nama }} ({{ $w->rt->nama_rt ?? '-' }})
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">No HP</label>
                    <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Skor Kelayakan (0-100)</label>
                    <input type="number" name="skor_kelayakan" class="form-control" value="{{ old('skor_kelayakan', 0) }}" min="0" max="100">
                    <small class="text-muted">Semakin tinggi semakin layak</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label">RT</label>
                    <select name="rt_id" class="form-select" disabled>
                        <option>Otomatis dari data warga</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Alasan Layak</label>
                    <textarea name="alasan_layak" class="form-control" rows="3" 
                              placeholder="Contoh: Keluarga kurang mampu, penghasilan di bawah UMR...">{{ old('alasan_layak') }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('bansos.penerima.index') }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Daftarkan Penerima</button>
        </div>
    </div>
</form>

@endsection
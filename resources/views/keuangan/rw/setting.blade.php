@extends('layouts.admin')

@section('title', 'Pengaturan Pembayaran RW')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">⚙️ Pengaturan Pembayaran RW</h5>
        <small class="text-muted">Atur QRIS & info bank untuk pembayaran iuran</small>
    </div>
    <a href="{{ route('keuangan.rw.dashboard') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

@if (session('success'))
<div class="alert alert-success">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
</div>
@endif

<form method="POST" action="{{ route('keuangan.rw.setting.store') }}" enctype="multipart/form-data">
    @csrf
    
    <div class="row g-3">
        {{-- QRIS --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <strong>📱 QRIS RW</strong>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Upload screenshot QRIS dari m-banking RW.
                        Warga akan scan QR ini untuk bayar.
                    </p>

                    @if ($rw->foto_qris)
                        <div class="text-center mb-3">
                            <img src="{{ $rw->foto_qris_url }}" class="img-fluid rounded" style="max-height: 250px;">
                        </div>
                        <a href="#" onclick="hapusQris(event)" class="btn btn-sm btn-outline-danger w-100 mb-2">
                            <i class="bi bi-trash"></i> Hapus QRIS
                        </a>
                    @else
                        <div class="text-center mb-3 text-muted">
                            <i class="bi bi-qr-code" style="font-size: 80px; opacity: 0.3;"></i>
                            <p>Belum ada QRIS</p>
                        </div>
                    @endif

                    <label class="form-label">Upload / Ganti QRIS</label>
                    <input type="file" name="foto_qris" class="form-control" accept="image/*">
                    <small class="text-muted">JPG/PNG, max 2MB</small>
                </div>
            </div>
        </div>

        {{-- Info Bank --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <strong>🏦 Info Transfer Bank</strong>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Isi info rekening RW untuk warga yang bayar via transfer.
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Bank</label>
                        <input type="text" name="bank_nama" class="form-control" 
                               value="{{ old('bank_nama', $rw->bank_nama) }}"
                               placeholder="Contoh: BCA, Mandiri, BRI">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">No. Rekening</label>
                        <input type="text" name="bank_rekening" class="form-control" 
                               value="{{ old('bank_rekening', $rw->bank_rekening) }}"
                               placeholder="1234567890">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Atas Nama</label>
                        <input type="text" name="bank_atas_nama" class="form-control" 
                               value="{{ old('bank_atas_nama', $rw->bank_atas_nama) }}"
                               placeholder="RW 07 Perumahan Rancamanyar">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kontak Bendahara</label>
                        <input type="text" name="kontak_bendahara" class="form-control" 
                               value="{{ old('kontak_bendahara', $rw->kontak_bendahara) }}"
                               placeholder="08xxxxxxxxxx">
                        <small class="text-muted">No HP bendahara untuk warga tanya</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tombol Simpan --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between">
                    <a href="{{ route('keuangan.rw.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-circle"></i> Simpan Pengaturan
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Form Hapus QRIS (hidden) --}}
<form id="formHapusQris" method="POST" action="{{ route('keuangan.rw.setting.hapus-qris') }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    function hapusQris(e) {
        e.preventDefault();
        if (confirm('Yakin hapus QRIS ini?')) {
            document.getElementById('formHapusQris').submit();
        }
    }
</script>
@endpush

@endsection
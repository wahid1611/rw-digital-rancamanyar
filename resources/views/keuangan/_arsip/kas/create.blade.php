@extends('layouts.admin')

@section('title', 'Catat Transaksi Kas')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-1">Catat Transaksi Kas</h5>
    <a href="{{ route('keuangan.kas.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('keuangan.kas.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Jenis <span class="text-danger">*</span></label>
                    <select name="jenis" id="jenis" class="form-select" required onchange="updateKategori()">
                        <option value="pemasukan" {{ old('jenis') == 'pemasukan' ? 'selected' : '' }}>💰 Pemasukan</option>
                        <option value="pengeluaran" {{ old('jenis') == 'pengeluaran' ? 'selected' : '' }}>💸 Pengeluaran</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select name="kategori" id="kategori" class="form-select" required>
                        <!-- Diisi JS -->
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nominal <span class="text-danger">*</span></label>
                    <input type="number" name="nominal" class="form-control" value="{{ old('nominal') }}" min="1" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="tgl_transaksi" class="form-control" 
                           value="{{ old('tgl_transaksi', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Bukti (opsional)</label>
                    <input type="file" name="bukti" class="form-control" accept="image/*">
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                    <textarea name="deskripsi" class="form-control" rows="3" required
                              placeholder="Jelaskan transaksi ini...">{{ old('deskripsi') }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('keuangan.kas.index') }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Simpan</button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    const kategoriPemasukan = {
        'iuran': 'Iuran',
        'sumbangan': 'Sumbangan',
        'bantuan': 'Bantuan',
        'denda': 'Denda',
        'lainnya': 'Lainnya',
    };
    const kategoriPengeluaran = {
        'operasional': 'Operasional',
        'perbaikan': 'Perbaikan',
        'kegiatan': 'Kegiatan',
        'sosial': 'Sosial',
        'lainnya': 'Lainnya',
    };

    function updateKategori() {
        const jenis = document.getElementById('jenis').value;
        const select = document.getElementById('kategori');
        const data = jenis === 'pemasukan' ? kategoriPemasukan : kategoriPengeluaran;
        
        select.innerHTML = '';
        for (const [key, label] of Object.entries(data)) {
            const opt = document.createElement('option');
            opt.value = key;
            opt.textContent = label;
            select.appendChild(opt);
        }
    }

    document.addEventListener('DOMContentLoaded', updateKategori);
</script>
@endpush

@endsection
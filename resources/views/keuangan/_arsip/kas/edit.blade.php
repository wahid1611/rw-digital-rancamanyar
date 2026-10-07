@extends('layouts.admin')

@section('title', 'Edit Transaksi Kas')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-1">Edit Transaksi Kas</h5>
    <a href="{{ route('keuangan.kas.show', $transaksi) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('keuangan.kas.update', $transaksi) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Jenis</label>
                    <select name="jenis" class="form-select" required>
                        <option value="pemasukan" {{ old('jenis', $transaksi->jenis) == 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="pengeluaran" {{ old('jenis', $transaksi->jenis) == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kategori</label>
                    <input type="text" name="kategori" class="form-control" 
                           value="{{ old('kategori', $transaksi->kategori) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nominal</label>
                    <input type="number" name="nominal" class="form-control" 
                           value="{{ old('nominal', $transaksi->nominal) }}" min="1" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tgl_transaksi" class="form-control" 
                           value="{{ old('tgl_transaksi', $transaksi->tgl_transaksi->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Ganti Bukti</label>
                    <input type="file" name="bukti" class="form-control" accept="image/*">
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3" required>{{ old('deskripsi', $transaksi->deskripsi) }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('keuangan.kas.show', $transaksi) }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Update</button>
        </div>
    </div>
</form>

@endsection
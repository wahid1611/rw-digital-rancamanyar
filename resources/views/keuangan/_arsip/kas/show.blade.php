@extends('layouts.admin')

@section('title', 'Detail Transaksi: ' . $transaksi->kode_transaksi)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Detail Transaksi Kas</h5>
        <small class="text-muted">Kode: <code>{{ $transaksi->kode_transaksi }}</code></small>
    </div>
    <div>
        @can('kas.edit')
        @if (!$transaksi->pembayaran_id)
        <a href="{{ route('keuangan.kas.edit', $transaksi) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil"></i> Edit
        </a>
        @endif
        @endcan
        <a href="{{ route('keuangan.kas.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="text-center mb-3">
            <span class="badge {{ $transaksi->jenis_badge }}" style="font-size: 14px; padding: 8px 15px;">
                {{ ucfirst($transaksi->jenis) }}
            </span>
            <h3 class="{{ $transaksi->jenis === 'pemasukan' ? 'text-success' : 'text-danger' }} mt-3">
                {{ $transaksi->jenis === 'pemasukan' ? '+' : '-' }} Rp {{ number_format($transaksi->nominal, 0, ',', '.') }}
            </h3>
        </div>

        <table class="table table-sm">
            <tr><th width="180">Kode</th><td><code>{{ $transaksi->kode_transaksi }}</code></td></tr>
            <tr><th>Tanggal</th><td>{{ $transaksi->tgl_transaksi->format('d F Y') }}</td></tr>
            <tr><th>Kategori</th><td>{{ $transaksi->kategori_label }}</td></tr>
            <tr><th>Deskripsi</th><td>{{ $transaksi->deskripsi }}</td></tr>
            <tr><th>Dicatat Oleh</th><td>{{ $transaksi->pencatat->name ?? '-' }}</td></tr>
            @if ($transaksi->pembayaran_id)
            <tr><th>Dari Pembayaran</th><td><code>{{ $transaksi->pembayaran->kode_pembayaran ?? '-' }}</code></td></tr>
            @endif
        </table>

        @if ($transaksi->bukti)
            <hr>
            <h6>Bukti Transaksi</h6>
            <a href="{{ route('file.preview', $transaksi->bukti) }}" target="_blank">
                <img src="{{ asset('storage/' . $transaksi->bukti) }}" class="img-fluid rounded" style="max-height: 300px;">
            </a>
        @endif
    </div>
</div>

@endsection
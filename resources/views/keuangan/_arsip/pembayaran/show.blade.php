@extends('layouts.admin')

@section('title', 'Detail Pembayaran: ' . $pembayaran->kode_pembayaran)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Detail Pembayaran</h5>
        <small class="text-muted">Kode: <code>{{ $pembayaran->kode_pembayaran }}</code></small>
    </div>
    <a href="{{ route('keuangan.pembayaran.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-center mb-3">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 60px;"></i>
                    <h4 class="text-success mt-2">Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</h4>
                </div>

                <table class="table table-sm">
                    <tr><th width="180">Kode Pembayaran</th><td><code>{{ $pembayaran->kode_pembayaran }}</code></td></tr>
                    <tr><th>Keluarga</th><td>{{ $pembayaran->keluarga->kepala_keluarga_nama ?? '-' }}</td></tr>
                    <tr><th>Tagihan</th><td>{{ $pembayaran->tagihan->kode_tagihan ?? '-' }}</td></tr>
                    <tr><th>Iuran</th><td>{{ $pembayaran->tagihan->iuran->nama ?? '-' }}</td></tr>
                    <tr><th>Metode</th><td>{{ $pembayaran->metode_label }}</td></tr>
                    <tr><th>Tanggal Bayar</th><td>{{ $pembayaran->tgl_bayar->format('d F Y') }}</td></tr>
                    @if ($pembayaran->no_referensi)
                    <tr><th>No Referensi</th><td>{{ $pembayaran->no_referensi }}</td></tr>
                    @endif
                    <tr><th>Dicatat Oleh</th><td>{{ $pembayaran->pencatat->name ?? '-' }}</td></tr>
                    @if ($pembayaran->catatan)
                    <tr><th>Catatan</th><td>{{ $pembayaran->catatan }}</td></tr>
                    @endif
                </table>

                @if ($pembayaran->bukti_url)
                    <hr>
                    <h6>Bukti Transfer</h6>
                    <a href="{{ route('file.preview', str_replace('/storage/', '', $pembayaran->bukti_url)) }}" target="_blank">
                        <img src="{{ $pembayaran->bukti_url }}" class="img-fluid rounded" style="max-height: 300px;">
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong>Status Tagihan</strong></div>
            <div class="card-body text-center">
                @if ($pembayaran->tagihan)
                    <span class="badge {{ $pembayaran->tagihan->status_badge }}" style="font-size: 14px; padding: 8px 15px;">
                        {{ $pembayaran->tagihan->status_label }}
                    </span>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Nominal:</small>
                        <strong>Rp {{ number_format($pembayaran->tagihan->nominal, 0, ',', '.') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Dibayar:</small>
                        <strong class="text-success">Rp {{ number_format($pembayaran->tagihan->total_dibayar, 0, ',', '.') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Tunggakan:</small>
                        <strong class="text-danger">Rp {{ number_format($pembayaran->tagihan->tunggakan, 0, ',', '.') }}</strong>
                    </div>
                @else
                    <p class="text-muted mb-0">Tidak terkait tagihan</p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
@extends('layouts.admin')

@section('title', 'Detail Penyaluran: ' . $penyaluran->kode_penyaluran)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Detail Penyaluran</h5>
        <small class="text-muted">Kode: <code>{{ $penyaluran->kode_penyaluran }}</code></small>
    </div>
    <div>
        @can('bansos.salurkan')
        <a href="{{ route('bansos.penyaluran.edit', $penyaluran) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil"></i> Edit
        </a>
        @endcan
        <a href="{{ route('bansos.penyaluran.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-center mb-3">
                    <span class="badge {{ $penyaluran->status_badge }}" style="font-size: 14px; padding: 8px 15px;">
                        {{ ucfirst($penyaluran->status) }}
                    </span>
                    <h3 class="text-success mt-3">Rp {{ number_format($penyaluran->nominal, 0, ',', '.') }}</h3>
                </div>

                <table class="table table-sm">
                    <tr><th width="180">Kode</th><td><code>{{ $penyaluran->kode_penyaluran }}</code></td></tr>
                    <tr><th>Penerima</th><td><strong>{{ $penyaluran->penerima->nama_penerima ?? '-' }}</strong></td></tr>
                    <tr><th>NIK</th><td>{{ $penyaluran->penerima->nik_penerima ?? '-' }}</td></tr>
                    <tr><th>RT</th><td>{{ $penyaluran->penerima->rt->nama_rt ?? '-' }}</td></tr>
                    <tr><th>Program</th><td>{{ $penyaluran->program->nama ?? '-' }}</td></tr>
                    <tr><th>Tanggal</th><td>{{ $penyaluran->tanggal->format('d F Y') }}</td></tr>
                    <tr><th>Periode</th><td>{{ $penyaluran->periode ?? '-' }}</td></tr>
                    <tr><th>Jenis</th><td>{{ $penyaluran->jenis_bantuan ?? '-' }}</td></tr>
                    @if ($penyaluran->deskripsi_barang)
                    <tr><th>Barang</th><td>{{ $penyaluran->deskripsi_barang }}</td></tr>
                    @endif
                    <tr><th>Dicatat Oleh</th><td>{{ $penyaluran->petugas->name ?? '-' }}</td></tr>
                    @if ($penyaluran->catatan)
                    <tr><th>Catatan</th><td>{{ $penyaluran->catatan }}</td></tr>
                    @endif
                </table>

                @if ($penyaluran->foto_bukti_url)
                    <hr>
                    <h6>Foto Bukti</h6>
                    <a href="{{ route('file.preview', $penyaluran->foto_bukti) }}" target="_blank">
                        <img src="{{ $penyaluran->foto_bukti_url }}" class="img-fluid rounded" style="max-height: 300px;">
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong>Penerima</strong></div>
            <div class="card-body text-center">
                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-2"
                     style="width: 60px; height: 60px; font-size: 24px; font-weight: 700;">
                    {{ strtoupper(substr($penyaluran->penerima->nama_penerima ?? 'A', 0, 1)) }}
                </div>
                <h6 class="mb-0">{{ $penyaluran->penerima->nama_penerima ?? '-' }}</h6>
                <small class="text-muted">{{ $penyaluran->penerima->rt->nama_rt ?? '-' }}</small>
                <hr>
                <a href="{{ route('bansos.penerima.show', $penyaluran->penerima_id) }}" class="btn btn-sm btn-outline-primary w-100">
                    <i class="bi bi-person"></i> Lihat Profil Penerima
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
@extends('layouts.admin')

@section('title', 'Detail Tagihan: ' . $tagihan->kode_tagihan)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Detail Tagihan</h5>
        <small class="text-muted">Kode: <code>{{ $tagihan->kode_tagihan }}</code></small>
    </div>
    <div>
        @can('tagihan.bayar')
        @if ($tagihan->status !== 'lunas')
        <a href="{{ route('keuangan.pembayaran.create', ['tagihan_id' => $tagihan->id]) }}" class="btn btn-success btn-sm">
            <i class="bi bi-cash"></i> Catat Pembayaran
        </a>
        @endif
        @endcan
        <a href="{{ route('keuangan.tagihan.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <span class="badge {{ $tagihan->status_badge }}" style="font-size: 14px;">
                        {{ $tagihan->status_label }}
                    </span>
                </div>

                <h5>{{ $tagihan->iuran->nama ?? '-' }}</h5>
                <p class="text-muted mb-3">Periode: {{ $tagihan->periode }}</p>

                <table class="table table-sm">
                    <tr><th width="180">Kode Tagihan</th><td><code>{{ $tagihan->kode_tagihan }}</code></td></tr>
                    <tr><th>Keluarga</th><td>{{ $tagihan->keluarga->kepala_keluarga_nama ?? '-' }} ({{ $tagihan->keluarga->no_kk ?? '-' }})</td></tr>
                    <tr><th>RT</th><td>{{ $tagihan->rt->nama_rt ?? '-' }}</td></tr>
                    <tr><th>Nominal</th><td><strong>Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</strong></td></tr>
                    <tr><th>Sudah Dibayar</th><td class="text-success">Rp {{ number_format($tagihan->total_dibayar, 0, ',', '.') }}</td></tr>
                    <tr><th>Tunggakan</th><td class="text-danger"><strong>Rp {{ number_format($tagihan->tunggakan, 0, ',', '.') }}</strong></td></tr>
                    <tr><th>Jatuh Tempo</th><td>{{ $tagihan->jatuh_tempo->format('d F Y') }}</td></tr>
                    @if ($tagihan->tgl_bayar_lunas)
                    <tr><th>Lunas Tanggal</th><td>{{ $tagihan->tgl_bayar_lunas->format('d F Y') }}</td></tr>
                    @endif
                </table>

                @if ($tagihan->keterangan)
                    <hr>
                    <p class="mb-0">{{ $tagihan->keterangan }}</p>
                @endif
            </div>
        </div>

        {{-- Riwayat Pembayaran --}}
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white"><strong><i class="bi bi-clock-history"></i> Riwayat Pembayaran</strong></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Tanggal</th>
                            <th>Metode</th>
                            <th>Nominal</th>
                            <th>Dicatat Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tagihan->pembayarans as $p)
                        <tr>
                            <td><code>{{ $p->kode_pembayaran }}</code></td>
                            <td>{{ $p->tgl_bayar->format('d M Y') }}</td>
                            <td><span class="badge bg-secondary">{{ $p->metode_label }}</span></td>
                            <td class="text-success">Rp {{ number_format($p->nominal, 0, ',', '.') }}</td>
                            <td><small>{{ $p->pencatat->name ?? '-' }}</small></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-3 text-muted">Belum ada pembayaran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="mb-2">
                    <i class="bi bi-receipt" style="font-size: 60px; color: #667eea;"></i>
                </div>
                <h4 class="mb-1">Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</h4>
                <p class="text-muted mb-0">{{ $tagihan->iuran->nama }}</p>
            </div>
        </div>
    </div>
</div>

@endsection
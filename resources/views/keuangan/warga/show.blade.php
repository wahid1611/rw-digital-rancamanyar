@extends('layouts.admin')

@section('title', 'Detail Tagihan')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Detail Tagihan</h5>
        <small class="text-muted">{{ $tagihan->kode_tagihan }}</small>
    </div>
    <a href="{{ route('keuangan.warga.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <span class="badge {{ $tagihan->status_badge }}" style="font-size: 14px; padding: 8px 15px;">
                        {{ $tagihan->status_label }}
                    </span>
                </div>

                <h5>{{ $tagihan->iuran->nama ?? '-' }}</h5>
                <p class="text-muted">Periode: {{ $tagihan->periode }}</p>

                <table class="table table-sm">
                    <tr><th width="180">Kode Tagihan</th><td><code>{{ $tagihan->kode_tagihan }}</code></td></tr>
                    <tr><th>Pemilik</th><td>{{ ucfirst($tagihan->pemilik) }}</td></tr>
                    <tr><th>Nominal</th><td><strong>Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</strong></td></tr>
                    <tr><th>Sudah Dibayar</th><td class="text-success">Rp {{ number_format($tagihan->total_dibayar, 0, ',', '.') }}</td></tr>
                    <tr><th>Tunggakan</th><td class="text-danger"><strong>Rp {{ number_format($tagihan->tunggakan, 0, ',', '.') }}</strong></td></tr>
                    <tr><th>Jatuh Tempo</th><td>{{ $tagihan->jatuh_tempo->format('d F Y') }}</td></tr>
                    @if ($tagihan->tgl_bayar_lunas)
                    <tr><th>Lunas Tanggal</th><td>{{ $tagihan->tgl_bayar_lunas->format('d F Y') }}</td></tr>
                    @endif
                </table>

                {{-- Riwayat Pembayaran --}}
                <hr>
                <h6>Riwayat Pembayaran</h6>
                @if ($tagihan->pembayarans->count() > 0)
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Tanggal</th>
                                <th>Metode</th>
                                <th class="text-end">Nominal</th>
                                <th>Kwitansi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tagihan->pembayarans as $p)
                            <tr>
                                <td><small><code>{{ $p->kode_pembayaran }}</code></small></td>
                                <td><small>{{ $p->tgl_bayar->format('d M Y') }}</small></td>
                                <td><small>{{ $p->metode_label }}</small></td>
                                <td class="text-end text-success"><strong>Rp {{ number_format($p->nominal, 0, ',', '.') }}</strong></td>
                                <td>
                                    <a href="{{ route('keuangan.warga.kwitansi', $p->id) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Download Kwitansi">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">Belum ada pembayaran.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        {{-- Info Cara Bayar --}}
        @if ($rw && $tagihan->status !== 'lunas')
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <strong><i class="bi bi-credit-card"></i> Cara Bayar</strong>
            </div>
            <div class="card-body">
                <p class="small text-muted">Bayar tagihan ini ke Bendahara / RW:</p>

                @if ($rw->bank_rekening)
                    <div class="mb-2">
                        <small class="text-muted">Transfer ke:</small>
                        <div><strong>{{ $rw->bank_nama }}</strong></div>
                        <div><code>{{ $rw->bank_rekening }}</code></div>
                        <div><small>{{ $rw->bank_atas_nama }}</small></div>
                    </div>
                @endif

                @if ($rw->foto_qris_url)
                    <div class="mb-2">
                        <small class="text-muted">QRIS:</small>
                        <div class="text-center mt-1">
                            <img src="{{ $rw->foto_qris_url }}" class="img-fluid rounded" style="max-height: 150px;">
                        </div>
                    </div>
                @endif

                @if ($rw->kontak_bendahara)
                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', $rw->kontak_bendahara) }}" 
                       target="_blank" class="btn btn-success btn-sm w-100 mb-2">
                        <i class="bi bi-whatsapp"></i> Hubungi Bendahara
                    </a>
                @endif
            </div>
        </div>
        @endif

        {{-- Info Tagihan --}}
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body text-center">
                <i class="bi bi-receipt" style="font-size: 50px; color: #667eea;"></i>
                <h4 class="mt-2 mb-1">Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</h4>
                <p class="text-muted mb-0 small">{{ $tagihan->iuran->nama }}</p>
            </div>
        </div>
    </div>
</div>

@endsection
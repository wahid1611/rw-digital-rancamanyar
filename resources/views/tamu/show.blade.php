@extends('layouts.admin')

@section('title', 'Detail Tamu: ' . $tamu->nama)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Detail Tamu</h5>
        <small class="text-muted">Kode: <code>{{ $tamu->kode_tamu }}</code></small>
    </div>
    <div>
        @can('tamu.checkout')
        @if ($tamu->status === 'masuk')
        <form method="POST" action="{{ route('tamu.checkout', $tamu) }}" class="d-inline"
              onsubmit="return confirm('Check-out tamu ini?')">
            @csrf
            <button class="btn btn-success btn-sm">
                <i class="bi bi-box-arrow-right"></i> Check-Out
            </button>
        </form>
        @endif
        @endcan
        @can('tamu.edit')
        <a href="{{ route('tamu.edit', $tamu) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil"></i> Edit
        </a>
        @endcan
        <a href="{{ route('tamu.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <span class="badge {{ $tamu->status_badge }}" style="font-size: 14px; padding: 8px 15px;">
                        {{ ucfirst(str_replace('_', ' ', $tamu->status)) }}
                    </span>
                </div>

                <h5>👤 {{ $tamu->nama }}</h5>
                @if ($tamu->instansi)
                    <p class="text-muted mb-3">{{ $tamu->instansi }}</p>
                @endif

                <table class="table table-sm">
                    <tr><th width="180">Kode Tamu</th><td><code>{{ $tamu->kode_tamu }}</code></td></tr>
                    <tr><th>No HP</th><td>{{ $tamu->no_hp ?? '-' }}</td></tr>
                    <tr><th>Identitas</th><td>{{ $tamu->jenis_identitas ?? '-' }} — {{ $tamu->no_identitas ?? '-' }}</td></tr>
                    <tr><th>Alamat Asal</th><td>{{ $tamu->alamat_asal ?? '-' }}</td></tr>
                    <tr><th>Tujuan</th><td>{{ $tamu->tujuan_label }}</td></tr>
                    <tr><th>Keperluan</th><td>{{ $tamu->keperluan }}</td></tr>
                    <tr><th>Kendaraan</th><td>{{ $tamu->jenis_kendaraan ?? '-' }} @if($tamu->plat_nomor) ({{ $tamu->plat_nomor }}) @endif</td></tr>
                    <tr><th>Waktu Masuk</th><td>{{ $tamu->waktu_masuk->format('d F Y, H:i') }}</td></tr>
                    @if ($tamu->waktu_keluar)
                    <tr><th>Waktu Keluar</th><td>{{ $tamu->waktu_keluar->format('d F Y, H:i') }}</td></tr>
                    <tr><th>Durasi</th><td>{{ $tamu->durasi_kunjungan }}</td></tr>
                    @else
                    <tr><th>Durasi</th><td class="text-warning">{{ $tamu->durasi_kunjungan }}</td></tr>
                    @endif
                    <tr><th>Dicatat Oleh</th><td>{{ $tamu->petugas->name ?? '-' }}</td></tr>
                    @if ($tamu->catatan)
                    <tr><th>Catatan</th><td>{{ $tamu->catatan }}</td></tr>
                    @endif
                </table>

                @if ($tamu->foto_tamu_url || $tamu->foto_ktp_url)
                <hr>
                <h6>Foto</h6>
                <div class="row g-2">
                    @if ($tamu->foto_tamu_url)
                    <div class="col-md-6">
                        <small class="text-muted">Foto Tamu</small><br>
                        <img src="{{ $tamu->foto_tamu_url }}" class="img-fluid rounded" style="max-height: 250px;">
                    </div>
                    @endif
                    @if ($tamu->foto_ktp_url)
                    <div class="col-md-6">
                        <small class="text-muted">Foto KTP</small><br>
                        <img src="{{ $tamu->foto_ktp_url }}" class="img-fluid rounded" style="max-height: 250px;">
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        {{-- QR Code --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white text-center"><strong><i class="bi bi-qr-code"></i> QR Code Tamu</strong></div>
            <div class="card-body text-center">
                <div id="qr-container"></div>
                <p class="small text-muted mt-2 mb-0">Tunjukkan QR ini saat check-out</p>
                <p class="small"><code>{{ $tamu->qr_code }}</code></p>
            </div>
        </div>

        {{-- Info Box --}}
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body">
                <div class="text-center">
                    <i class="bi bi-clock-history" style="font-size: 40px; color: #667eea;"></i>
                    <h6 class="mt-2">Waktu Masuk</h6>
                    <p class="mb-0"><strong>{{ $tamu->waktu_masuk->format('H:i') }}</strong></p>
                    <small class="text-muted">{{ $tamu->waktu_masuk->format('d F Y') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    new QRCode(document.getElementById("qr-container"), {
        text: "{{ $tamu->qr_code }}",
        width: 180,
        height: 180,
        colorDark: "#1e293b",
        colorLight: "#ffffff",
    });
</script>
@endpush

@endsection
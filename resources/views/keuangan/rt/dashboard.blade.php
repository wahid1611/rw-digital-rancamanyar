@extends('layouts.admin')

@section('title', 'Keuangan ' . $rt->nama_rt)

@push('styles')
<style>
    .stat-card {
        border-radius: 8px;
        padding: 18px;
        color: #fff;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s;
    }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-card .icon-bg {
        position: absolute;
        right: -10px;
        bottom: -10px;
        font-size: 60px;
        opacity: 0.15;
    }
    .stat-card h3 { font-size: 22px; font-weight: 700; margin: 5px 0 0; }
    .stat-card .label { font-size: 12px; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-card small { font-size: 12px; opacity: 0.85; }
    .gradient-blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .gradient-green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .gradient-red { background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%); }
    .gradient-orange { background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%); }

    .big-btn {
        padding: 18px;
        border-radius: 8px;
        text-align: center;
        font-size: 14px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        color: #fff;
        text-decoration: none;
        display: block;
    }
    .big-btn:hover { color: #fff; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0,0,0,0.15); }
    .big-btn i { font-size: 24px; display: block; margin-bottom: 6px; }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Keuangan {{ $rt->nama_rt }}</h5>
        <small class="text-muted">Kelola uang kas {{ $rt->nama_rt }} Perumahan Rancamanyar</small>
    </div>
    <div>
        <a href="{{ route('keuangan.rt.laporan') }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-file-earmark-text"></i> Laporan
        </a>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6 col-lg-3">
        <div class="stat-card gradient-green">
            <i class="bi bi-wallet2 icon-bg"></i>
            <div class="label">Saldo Kas</div>
            <h3>Rp {{ number_format($saldo, 0, ',', '.') }}</h3>
            <small>Uang {{ $rt->nama_rt }} saat ini</small>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card gradient-blue">
            <i class="bi bi-arrow-down-circle icon-bg"></i>
            <div class="label">Uang Masuk</div>
            <h3>Rp {{ number_format($pemasukanBulanIni, 0, ',', '.') }}</h3>
            <small>Bulan {{ now()->translatedFormat('F Y') }}</small>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card gradient-red">
            <i class="bi bi-arrow-up-circle icon-bg"></i>
            <div class="label">Uang Keluar</div>
            <h3>Rp {{ number_format($pengeluaranBulanIni, 0, ',', '.') }}</h3>
            <small>Bulan {{ now()->translatedFormat('F Y') }}</small>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card gradient-orange">
            <i class="bi bi-exclamation-triangle icon-bg"></i>
            <div class="label">Belum Bayar</div>
            <h3>{{ $belumBayar }} KK</h3>
            <small>Tunggakan Rp {{ number_format($tunggakan, 0, ',', '.') }}</small>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        @can('keuangan_rt.create')
        <a href="{{ route('keuangan.rt.masuk.create') }}" class="big-btn" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
            <i class="bi bi-cash-coin"></i>
            CATAT UANG MASUK
            <small class="d-block mt-1" style="font-size: 12px; opacity: 0.9;">Klik kalau ada warga bayar</small>
        </a>
        @endcan
    </div>
    <div class="col-md-4">
        @can('keuangan_rt.create')
        <a href="{{ route('keuangan.rt.keluar.create') }}" class="big-btn" style="background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);">
            <i class="bi bi-cart-dash"></i>
            CATAT UANG KELUAR
            <small class="d-block mt-1" style="font-size: 12px; opacity: 0.9;">Klik kalau RT belanja</small>
        </a>
        @endcan
    </div>
    <div class="col-md-4">
        <a href="{{ route('keuangan.rt.belum-bayar') }}" class="big-btn" style="background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%); color: #333;">
            <i class="bi bi-people"></i>
            LIHAT YANG BELUM BAYAR
            <small class="d-block mt-1" style="font-size: 12px;">{{ $belumBayar }} KK belum bayar</small>
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <strong><i class="bi bi-graph-up"></i> Grafik 6 Bulan Terakhir</strong>
            </div>
            <div class="card-body">
                @php
                    $maxValue = max(array_merge(
                        array_column($grafik, 'masuk'),
                        array_column($grafik, 'keluar'),
                        [1]
                    ));
                @endphp
                @foreach ($grafik as $g)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <strong>{{ $g['bulan'] }}</strong>
                            <span>
                                <span class="text-success">+Rp {{ number_format($g['masuk'], 0, ',', '.') }}</span>
                                |
                                <span class="text-danger">-Rp {{ number_format($g['keluar'], 0, ',', '.') }}</span>
                            </span>
                        </div>
                        <div class="progress mb-1" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ ($g['masuk'] / $maxValue) * 100 }}%"></div>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-danger" style="width: {{ ($g['keluar'] / $maxValue) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <strong><i class="bi bi-clock-history"></i> Pembayaran Terbaru</strong>
            </div>
            <div class="card-body p-0">
                @forelse ($pembayaranTerbaru as $p)
                    <div class="p-3 border-bottom">
                        <div class="d-flex justify-content-between">
                            <strong class="small">{{ $p->keluarga->kepala_keluarga_nama ?? '-' }}</strong>
                            <span class="text-success fw-bold small">+Rp {{ number_format($p->nominal, 0, ',', '.') }}</span>
                        </div>
                        <small class="text-muted" style="font-size: 12px;">
                            {{ $p->tagihan->iuran->nama ?? '-' }} • {{ $p->tgl_bayar->format('d M Y') }}
                        </small>
                    </div>
                @empty
                    <div class="p-3 text-center text-muted">
                        <i class="bi bi-inbox" style="font-size: 30px;"></i>
                        <p class="mb-0 small">Belum ada pembayaran</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<a href="#" class="btn btn-info position-fixed"
   style="bottom: 20px; right: 20px; border-radius: 50%; width: 50px; height: 50px; font-size: 20px; box-shadow: 0 3px 10px rgba(0,0,0,0.2);"
   onclick="showBantuan(); return false;" title="Bantuan">
    <i class="bi bi-question-lg"></i>
</a>

<div class="modal fade" id="modalBantuan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h6 class="modal-title"><i class="bi bi-question-circle"></i> Bantuan — Keuangan {{ $rt->nama_rt }}</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="font-size: 14px;">
                <p><strong>Saldo Kas</strong> = uang {{ $rt->nama_rt }} saat ini.</p>
                <p><strong>Uang Masuk</strong> = uang yang masuk bulan ini.</p>
                <p><strong>Uang Keluar</strong> = uang yang dibelanjakan bulan ini.</p>
                <p><strong>Belum Bayar</strong> = warga yang belum bayar iuran.</p>
                <hr>
                <p class="mb-0"><strong>Cara pakai:</strong></p>
                <ol class="mb-2">
                    <li>Klik <span class="badge bg-success">CATAT UANG MASUK</span> kalau ada warga bayar</li>
                    <li>Klik <span class="badge bg-danger">CATAT UANG KELUAR</span> kalau RT belanja</li>
                    <li>Klik <span class="badge bg-warning text-dark">LIHAT YANG BELUM BAYAR</span></li>
                </ol>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">Mengerti</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showBantuan() {
        new bootstrap.Modal(document.getElementById('modalBantuan')).show();
    }
</script>
@endpush

@endsection
@extends('layouts.admin')

@section('title', 'Dashboard Keuangan')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1"> Dashboard Keuangan RW</h5>
        <small class="text-muted">Ringkasan keuangan & iuran warga</small>
    </div>
    @can('keuangan.export')
    <a href="#" class="btn btn-outline-success btn-sm" onclick="alert('Export PDF akan tersedia di tahap berikutnya')">
        <i class="bi bi-download"></i> Export Laporan
    </a>
    @endcan
</div>

{{-- STATISTIK UTAMA --}}
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-muted small">Saldo Kas</div>
                        <h4 class="mb-0 {{ $saldo >= 0 ? 'text-success' : 'text-danger' }}">
                            Rp {{ number_format($saldo, 0, ',', '.') }}
                        </h4>
                    </div>
                    <i class="bi bi-wallet2" style="font-size: 40px; color: #10b981; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-muted small">Pemasukan Bulan Ini</div>
                        <h4 class="mb-0 text-primary">
                            Rp {{ number_format($pemasukanBulanIni, 0, ',', '.') }}
                        </h4>
                    </div>
                    <i class="bi bi-arrow-down-circle" style="font-size: 40px; color: #3b82f6; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-muted small">Pengeluaran Bulan Ini</div>
                        <h4 class="mb-0 text-danger">
                            Rp {{ number_format($pengeluaranBulanIni, 0, ',', '.') }}
                        </h4>
                    </div>
                    <i class="bi bi-arrow-up-circle" style="font-size: 40px; color: #ef4444; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-muted small">Total Tunggakan</div>
                        <h4 class="mb-0 text-warning">
                            Rp {{ number_format($tunggakan, 0, ',', '.') }}
                        </h4>
                    </div>
                    <i class="bi bi-exclamation-triangle" style="font-size: 40px; color: #f59e0b; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- STATUS TAGIHAN --}}
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ $totalTagihan }}</h3>
                <small class="text-muted">Total Tagihan</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <h3 class="mb-0 text-danger">{{ $belumBayar }}</h3>
                <small class="text-muted">Belum Bayar</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <h3 class="mb-0 text-success">{{ $lunas }}</h3>
                <small class="text-muted">Lunas</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Grafik Pemasukan/Pengeluaran --}}
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <strong><i class="bi bi-graph-up"></i> Grafik Keuangan 6 Bulan Terakhir</strong>
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
                            <div class="progress-bar bg-success" 
                                 style="width: {{ ($g['masuk'] / $maxValue) * 100 }}%"></div>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-danger" 
                                 style="width: {{ ($g['keluar'] / $maxValue) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Tunggakan Per RT --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <strong><i class="bi bi-geo-alt"></i> Tunggakan per RT</strong>
            </div>
            <div class="card-body" style="max-height: 350px; overflow-y: auto;">
                @forelse ($tunggakanPerRt as $t)
                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                        <div>
                            <strong>{{ $t->rt->nama_rt ?? '-' }}</strong><br>
                            <small class="text-muted">{{ $t->jumlah }} KK belum bayar</small>
                        </div>
                        <div class="text-end">
                            <strong class="text-danger">Rp {{ number_format($t->total, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center mb-0">Tidak ada tunggakan. 🎉</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Pembayaran Terbaru --}}
<div class="card border-0 shadow-sm mt-3">
    <div class="card-header bg-white d-flex justify-content-between">
        <strong><i class="bi bi-clock-history"></i> Pembayaran Terbaru</strong>
        @can('tagihan.view')
        <a href="{{ route('keuangan.pembayaran.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
        @endcan
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Keluarga</th>
                    <th>Iuran</th>
                    <th>Nominal</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pembayaranTerbaru as $p)
                <tr>
                    <td><code>{{ $p->kode_pembayaran }}</code></td>
                    <td>{{ $p->keluarga->kepala_keluarga_nama ?? '-' }}</td>
                    <td><small>{{ $p->tagihan->iuran->nama ?? '-' }}</small></td>
                    <td><strong class="text-success">Rp {{ number_format($p->nominal, 0, ',', '.') }}</strong></td>
                    <td><small>{{ $p->tgl_bayar->format('d M Y') }}</small></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-3 text-muted">Belum ada pembayaran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
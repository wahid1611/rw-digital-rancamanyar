@extends('layouts.admin')

@section('title', 'Rekap Keuangan Semua RT')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Rekap Keuangan Semua RT</h5>
        <small class="text-muted">Pantau keuangan RW 07 + semua RT (read-only)</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('keuangan.rekap.export') }}" class="btn btn-sm btn-success">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
        </a>
        <a href="{{ route('keuangan.rekap.laporan') }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-file-earmark-text"></i> Laporan Gabungan
        </a>
    </div>
</div>

{{-- Statistik Utama --}}
<div class="row g-3 mb-3">
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm gradient-blue">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="small opacity-75">Saldo Kas RW</div>
                        <h3 class="mb-0">Rp {{ number_format($saldoRw, 0, ',', '.') }}</h3>
                    </div>
                    <i class="bi bi-wallet2" style="font-size: 40px; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm gradient-green">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="small opacity-75">Total Saldo Semua RT</div>
                        <h3 class="mb-0">Rp {{ number_format($totalSaldoRt, 0, ',', '.') }}</h3>
                    </div>
                    <i class="bi bi-house-door" style="font-size: 40px; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="small opacity-75">Total Uang RW 07</div>
                        <h4 class="mb-0">Rp {{ number_format($totalSemua, 0, ',', '.') }}</h4>
                        <small>RW + Semua RT</small>
                    </div>
                    <i class="bi bi-cash-stack" style="font-size: 40px; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm gradient-orange">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="small opacity-75">Total Tunggakan RT</div>
                        <h4 class="mb-0">Rp {{ number_format($totalTunggakanRt, 0, ',', '.') }}</h4>
                    </div>
                    <i class="bi bi-exclamation-triangle" style="font-size: 40px; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Ringkasan Tagihan --}}
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-muted small">Total Tagihan</div>
                        <h4 class="mb-0">{{ $totalTagihan }}</h4>
                    </div>
                    <div class="text-end">
                        <div class="text-muted small">Belum Bayar</div>
                        <h4 class="mb-0 text-danger">{{ $belumBayar }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Persentase Kelunasan</div>
                @php
                    $persen = $totalTagihan > 0 ? round((($totalTagihan - $belumBayar) / $totalTagihan) * 100) : 0;
                @endphp
                <div class="d-flex align-items-center gap-2">
                    <h4 class="mb-0">{{ $persen }}%</h4>
                    <div class="progress flex-grow-1" style="height: 10px;">
                        <div class="progress-bar bg-success" style="width: {{ $persen }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Rekap per RT --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <strong>📊 Rekap Keuangan per RT</strong>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th width="50">#</th>
                    <th>RT</th>
                    <th class="text-end">Uang Masuk</th>
                    <th class="text-end">Uang Keluar</th>
                    <th class="text-end">Saldo</th>
                    <th class="text-end">Tunggakan</th>
                    <th class="text-center">Lunas</th>
                    <th width="80" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rekapRts as $i => $r)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $r['rt']->nama_rt }}</strong></td>
                    <td class="text-end text-success">Rp {{ number_format($r['pemasukan'], 0, ',', '.') }}</td>
                    <td class="text-end text-danger">Rp {{ number_format($r['pengeluaran'], 0, ',', '.') }}</td>
                    <td class="text-end">
                        <strong class="{{ $r['saldo'] >= 0 ? 'text-primary' : 'text-danger' }}">
                            Rp {{ number_format($r['saldo'], 0, ',', '.') }}
                        </strong>
                    </td>
                    <td class="text-end text-warning">Rp {{ number_format($r['tunggakan'], 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="badge bg-success">{{ $r['lunas_kk'] }}</span> /
                        <span class="badge bg-secondary">{{ $r['total_kk'] }}</span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('keuangan.rekap.rt', $r['rt']->id) }}" 
                           class="btn btn-sm btn-outline-info" title="Detail">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox" style="font-size: 40px;"></i>
                        <p class="mb-0 mt-2">Belum ada data RT.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if (count($rekapRts) > 0)
            <tfoot class="table-light">
                <tr>
                    <th colspan="2" class="text-end">TOTAL SEMUA RT:</th>
                    <th class="text-end text-success">
                        Rp {{ number_format(collect($rekapRts)->sum('pemasukan'), 0, ',', '.') }}
                    </th>
                    <th class="text-end text-danger">
                        Rp {{ number_format(collect($rekapRts)->sum('pengeluaran'), 0, ',', '.') }}
                    </th>
                    <th class="text-end text-primary">
                        Rp {{ number_format($totalSaldoRt, 0, ',', '.') }}
                    </th>
                    <th class="text-end text-warning">
                        Rp {{ number_format($totalTunggakanRt, 0, ',', '.') }}
                    </th>
                    <th colspan="2"></th>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

{{-- Tombol Bantuan --}}
<a href="#" class="btn btn-info position-fixed"
   style="bottom: 20px; right: 20px; border-radius: 50%; width: 50px; height: 50px; font-size: 20px; box-shadow: 0 3px 10px rgba(0,0,0,0.2);"
   onclick="showBantuan(); return false;" title="Bantuan">
    <i class="bi bi-question-lg"></i>
</a>

{{-- Modal Bantuan --}}
<div class="modal fade" id="modalBantuan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h6 class="modal-title"><i class="bi bi-question-circle"></i> Bantuan — Rekap Keuangan</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="font-size: 14px;">
                <p><strong>Saldo Kas RW</strong> = uang RW saat ini.</p>
                <p><strong>Total Saldo Semua RT</strong> = jumlah saldo 15 RT.</p>
                <p><strong>Total Uang RW 07</strong> = RW + semua RT.</p>
                <p><strong>Tunggakan RT</strong> = total iuran belum dibayar.</p>
                <hr>
                <p class="mb-0"><strong>Cara pakai:</strong></p>
                <ol class="mb-2">
                    <li>Lihat tabel di bawah untuk membandingkan RT</li>
                    <li>Klik <i class="bi bi-eye"></i> untuk detail RT tertentu</li>
                    <li>Klik <strong>Laporan Gabungan</strong> untuk cetak</li>
                </ol>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">Mengerti</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .gradient-blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .gradient-green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .gradient-orange { background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%); }
</style>
@endpush

@push('scripts')
<script>
    function showBantuan() {
        new bootstrap.Modal(document.getElementById('modalBantuan')).show();
    }
</script>
@endpush

@endsection
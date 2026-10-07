@extends('layouts.admin')

@section('title', 'Tagihan Saya')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Tagihan Saya</h5>
        <small class="text-muted">Daftar tagihan iuran & riwayat pembayaran Anda</small>
    </div>
    <a href="{{ route('keuangan.warga.grafik') }}" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-graph-up"></i> Lihat Grafik Keuangan RW
    </a>
</div>

{{-- Ringkasan --}}
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Tagihan</div>
                <h4 class="mb-0">{{ $totalTagihan }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Sudah Lunas</div>
                <h4 class="mb-0 text-success">{{ $lunas }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Belum Bayar</div>
                <h4 class="mb-0 text-danger">{{ $belumBayar }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Tunggakan</div>
                <h4 class="mb-0 text-warning">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
</div>

{{-- Info Cara Bayar --}}
@if ($rw && ($rw->foto_qris || $rw->bank_rekening))
<div class="card border-0 shadow-sm mb-3" style="background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-1">Cara Bayar Iuran</h6>
                <small class="text-muted">
                    Transfer / QRIS / Tunai ke Bendahara
                    @if ($rw->kontak_bendahara)
                        • <i class="bi bi-telephone"></i> {{ $rw->kontak_bendahara }}
                    @endif
                </small>
            </div>
            <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalCaraBayar">
                Lihat Cara Bayar
            </a>
        </div>
    </div>
</div>
@endif

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('keuangan.warga.index') }}" class="row g-2">
            <div class="col-md-4">
                <label class="form-label small">Periode</label>
                <select name="periode" class="form-select form-select-sm">
                    <option value="">Semua Periode</option>
                    @foreach ($periodes as $p)
                        <option value="{{ $p }}" {{ $periode == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="belum_bayar" {{ $status == 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                    <option value="sebagian" {{ $status == 'sebagian' ? 'selected' : '' }}>Sebagian</option>
                    <option value="lunas" {{ $status == 'lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-search"></i> Tampilkan
                </button>
                <a href="{{ route('keuangan.warga.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tabel Tagihan --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th width="50">#</th>
                    <th>Iuran</th>
                    <th>Periode</th>
                    <th>Jatuh Tempo</th>
                    <th class="text-end">Nominal</th>
                    <th class="text-end">Tunggakan</th>
                    <th>Status</th>
                    <th width="100" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tagihans as $i => $t)
                <tr>
                    <td>{{ $tagihans->firstItem() + $i }}</td>
                    <td>
                        <strong>{{ $t->iuran->nama ?? '-' }}</strong>
                        <br><small class="text-muted">{{ ucfirst($t->pemilik) }} • {{ $t->iuran->kategori ?? '-' }}</small>
                    </td>
                    <td><span class="badge bg-secondary">{{ $t->periode }}</span></td>
                    <td>
                        <small>{{ $t->jatuh_tempo->format('d M Y') }}</small>
                        @if ($t->isTelat() && $t->status !== 'lunas')
                            <br><span class="badge bg-danger">Telat</span>
                        @endif
                    </td>
                    <td class="text-end">Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                    <td class="text-end">
                        @if ($t->tunggakan > 0)
                            <strong class="text-danger">Rp {{ number_format($t->tunggakan, 0, ',', '.') }}</strong>
                        @else
                            <span class="text-success">-</span>
                        @endif
                    </td>
                    <td><span class="badge {{ $t->status_badge }}">{{ $t->status_label }}</span></td>
                    <td class="text-center">
                        <a href="{{ route('keuangan.warga.show', $t->id) }}" class="btn btn-sm btn-outline-info" title="Detail">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="bi bi-check-circle text-success" style="font-size: 40px;"></i>
                        <p class="mb-0 mt-2">Tidak ada tagihan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($tagihans->hasPages())
    <div class="card-footer bg-white">{{ $tagihans->links() }}</div>
    @endif
</div>

{{-- Modal Cara Bayar --}}
@if ($rw)
<div class="modal fade" id="modalCaraBayar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h6 class="modal-title"><i class="bi bi-credit-card"></i> Cara Bayar Iuran</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if ($rw->bank_rekening)
                    <div class="mb-3">
                        <h6 class="text-primary">Transfer Bank</h6>
                        <table class="table table-sm mb-0">
                            <tr><td width="120">Bank</td><td><strong>{{ $rw->bank_nama }}</strong></td></tr>
                            <tr><td>No. Rekening</td><td><strong>{{ $rw->bank_rekening }}</strong></td></tr>
                            <tr><td>Atas Nama</td><td><strong>{{ $rw->bank_atas_nama }}</strong></td></tr>
                        </table>
                    </div>
                @endif

                @if ($rw->foto_qris_url)
                    <div class="mb-3">
                        <h6 class="text-primary">QRIS (semua e-wallet & m-banking)</h6>
                        <div class="text-center">
                            <img src="{{ $rw->foto_qris_url }}" class="img-fluid rounded" style="max-height: 250px;">
                        </div>
                        <small class="text-muted d-block text-center mt-2">
                            Scan dengan GoPay, OVO, Dana, ShopeePay, LinkAja, m-banking
                        </small>
                    </div>
                @endif

                @if ($rw->kontak_bendahara)
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-telephone"></i> Konfirmasi ke Bendahara: <strong>{{ $rw->kontak_bendahara }}</strong>
                    </div>
                @endif

                <div class="alert alert-warning mt-3 mb-0 small">
                    <i class="bi bi-info-circle"></i>
                    Setelah bayar, upload bukti ke halaman detail tagihan.
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection
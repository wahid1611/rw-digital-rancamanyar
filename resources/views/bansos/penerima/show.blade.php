@extends('layouts.admin')

@section('title', 'Detail Penerima: ' . $penerima->nama_penerima)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">{{ $penerima->nama_penerima }}</h5>
        <small class="text-muted">Program: {{ $penerima->program->nama ?? '-' }}</small>
    </div>
    <div>
        @can('bansos.salurkan')
        @if ($penerima->status_kelayakan === 'layak')
        <a href="{{ route('bansos.penyaluran.create', ['penerima_id' => $penerima->id]) }}" class="btn btn-success btn-sm">
            <i class="bi bi-cash-coin"></i> Catat Penyaluran
        </a>
        @endif
        @endcan
        <a href="{{ route('bansos.penerima.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3"
                     style="width: 80px; height: 80px; font-size: 32px; font-weight: 700;">
                    {{ strtoupper(substr($penerima->nama_penerima, 0, 1)) }}
                </div>
                <h5 class="mb-1">{{ $penerima->nama_penerima }}</h5>
                <p class="text-muted small">{{ $penerima->nik_penerima }}</p>
                <span class="badge {{ $penerima->status_kelayakan_badge }}">{{ $penerima->status_kelayakan_label }}</span>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white"><strong>Info</strong></div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><th>Program</th><td>{{ $penerima->program->nama ?? '-' }}</td></tr>
                    <tr><th>RT</th><td>{{ $penerima->rt->nama_rt ?? '-' }}</td></tr>
                    <tr><th>No HP</th><td>{{ $penerima->no_hp ?? '-' }}</td></tr>
                    <tr><th>Skor</th><td>{{ $penerima->skor_kelayakan }} / 100</td></tr>
                    <tr><th>Total Diterima</th><td>
                        <strong class="text-success">Rp {{ number_format($penerima->total_diterima, 0, ',', '.') }}</strong>
                    </td></tr>
                </table>
            </div>
        </div>

        {{-- Verifikasi --}}
        @can('bansos.verifikasi')
        @if ($penerima->status_kelayakan === 'pending')
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-warning text-dark"><strong><i class="bi bi-shield-check"></i> Verifikasi Kelayakan</strong></div>
            <div class="card-body">
                <form method="POST" action="{{ route('bansos.penerima.verifikasi', $penerima) }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small">Status</label>
                        <select name="status_kelayakan" class="form-select form-select-sm" required>
                            <option value="layak">✅ Layak</option>
                            <option value="tidak_layak">❌ Tidak Layak</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Catatan</label>
                        <textarea name="catatan_verifikasi" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm w-100">
                        <i class="bi bi-check-circle"></i> Verifikasi
                    </button>
                </form>
            </div>
        </div>
        @elseif ($penerima->verifikator)
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body">
                <small class="text-muted">
                    Diverifikasi oleh <strong>{{ $penerima->verifikator->name }}</strong><br>
                    {{ $penerima->verified_at?->format('d F Y, H:i') }}
                </small>
                @if ($penerima->catatan_verifikasi)
                    <hr>
                    <small>{{ $penerima->catatan_verifikasi }}</small>
                @endif
            </div>
        </div>
        @endif
        @endcan
    </div>

    <div class="col-md-8">
        @if ($penerima->alasan_layak)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong>Alasan Layak</strong></div>
            <div class="card-body">
                <p class="mb-0">{{ $penerima->alasan_layak }}</p>
            </div>
        </div>
        @endif

        {{-- Riwayat Penyaluran --}}
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white"><strong><i class="bi bi-clock-history"></i> Riwayat Penyaluran ({{ $penerima->penyalurans->count() }})</strong></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Tanggal</th>
                            <th>Periode</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th width="80" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($penerima->penyalurans->sortByDesc('tanggal') as $s)
                        <tr>
                            <td><code>{{ $s->kode_penyaluran }}</code></td>
                            <td>{{ $s->tanggal->format('d M Y') }}</td>
                            <td>{{ $s->periode ?? '-' }}</td>
                            <td class="text-success"><strong>Rp {{ number_format($s->nominal, 0, ',', '.') }}</strong></td>
                            <td><span class="badge {{ $s->status_badge }}">{{ ucfirst($s->status) }}</span></td>
                            <td class="text-center">
                                <a href="{{ route('bansos.penyaluran.show', $s) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-3 text-muted">Belum ada penyaluran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
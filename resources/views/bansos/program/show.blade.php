@extends('layouts.admin')

@section('title', 'Detail Program: ' . $program->nama)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">{{ $program->nama }}</h5>
        <small class="text-muted">Kode: <code>{{ $program->kode_program }}</code></small>
    </div>
    <div>
        @can('bansos.create')
        <a href="{{ route('bansos.penerima.create', ['program_id' => $program->id]) }}" class="btn btn-success btn-sm">
            <i class="bi bi-plus-circle"></i> Tambah Penerima
        </a>
        @endcan
        @can('bansos.edit')
        <a href="{{ route('bansos.program.edit', $program) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil"></i> Edit
        </a>
        @endcan
        <a href="{{ route('bansos.program.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <span class="badge bg-info text-dark">{{ ucfirst($program->kategori) }}</span>
                    <span class="badge {{ $program->status_badge }}">{{ ucfirst($program->status) }}</span>
                </div>

                <table class="table table-sm mb-0">
                    <tr><th>Kode</th><td><code>{{ $program->kode_program }}</code></td></tr>
                    <tr><th>Nama</th><td><strong>{{ $program->nama }}</strong></td></tr>
                    <tr><th>Sumber</th><td>{{ $program->sumber ?? '-' }}</td></tr>
                    <tr><th>Jenis</th><td>{{ $program->jenis_bantuan_label }}</td></tr>
                    <tr><th>Nominal</th><td>
                        @if ($program->nominal)
                            Rp {{ number_format($program->nominal, 0, ',', '.') }} {{ $program->satuan_bantuan }}
                        @else
                            -
                        @endif
                    </td></tr>
                    <tr><th>Periode</th><td>{{ $program->periode ?? '-' }}</td></tr>
                    <tr><th>Mulai</th><td>{{ $program->tanggal_mulai->format('d F Y') }}</td></tr>
                    <tr><th>Selesai</th><td>{{ $program->tanggal_selesai?->format('d F Y') ?? '-' }}</td></tr>
                    <tr><th>Dibuat</th><td><small>{{ $program->pembuat->name ?? '-' }}</small></td></tr>
                </table>

                @if ($program->deskripsi)
                    <hr>
                    <small class="text-muted">{{ $program->deskripsi }}</small>
                @endif

                @if ($program->kriteria)
                    <hr>
                    <strong>Kriteria:</strong>
                    <p class="mb-0 small">{{ $program->kriteria }}</p>
                @endif
            </div>
        </div>

        {{-- Statistik --}}
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="mb-0 text-primary">{{ $program->penerimas->count() }}</h4>
                        <small class="text-muted">Penerima</small>
                    </div>
                    <div class="col-6">
                        <h4 class="mb-0 text-success">{{ $program->penyalurans->count() }}</h4>
                        <small class="text-muted">Penyaluran</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong><i class="bi bi-people"></i> Daftar Penerima ({{ $program->penerimas->count() }})</strong>
                <a href="{{ route('bansos.penerima.index', ['program_id' => $program->id]) }}" class="btn btn-sm btn-outline-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>RT</th>
                            <th>Skor</th>
                            <th>Status</th>
                            <th width="80" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($program->penerimas->take(10) as $p)
                        <tr>
                            <td><strong>{{ $p->nama_penerima }}</strong><br><small class="text-muted">{{ $p->nik_penerima }}</small></td>
                            <td>{{ $p->rt->nama_rt ?? '-' }}</td>
                            <td>{{ $p->skor_kelayakan }}</td>
                            <td><span class="badge {{ $p->status_kelayakan_badge }}">{{ $p->status_kelayakan_label }}</span></td>
                            <td class="text-center">
                                <a href="{{ route('bansos.penerima.show', $p) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-3 text-muted">Belum ada penerima.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
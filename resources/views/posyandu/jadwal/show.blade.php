@extends('layouts.admin')

@section('title', $jadwal->nama)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">{{ $jadwal->nama }}</h5>
        <small class="text-muted">
            {{ $jadwal->tanggal->format('d F Y') }}
            @if ($jadwal->jam_mulai)
                • {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
            @endif
        </small>
    </div>
    <div>
        @can('posyandu.periksa')
        <a href="{{ route('posyandu.pemeriksaan.create', ['jadwal_id' => $jadwal->id]) }}" class="btn btn-success btn-sm">
            <i class="bi bi-clipboard-plus"></i> Tambah Pemeriksaan
        </a>
        @endcan
        @can('posyandu.edit')
        <a href="{{ route('posyandu.jadwal.edit', $jadwal) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil"></i> Edit
        </a>
        @endcan
        <a href="{{ route('posyandu.jadwal.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong>Info Jadwal</strong></div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><th>Nama</th><td>{{ $jadwal->nama }}</td></tr>
                    <tr><th>Tanggal</th><td>{{ $jadwal->tanggal->format('d F Y') }}</td></tr>
                    <tr><th>Jam</th><td>
                        {{ $jadwal->jam_mulai ? \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') : '-' }} -
                        {{ $jadwal->jam_selesai ? \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') : '-' }}
                    </td></tr>
                    <tr><th>Jenis</th><td>{{ $jadwal->jenis_label }}</td></tr>
                    <tr><th>Lokasi</th><td>{{ $jadwal->lokasi ?? '-' }}</td></tr>
                    <tr><th>Status</th><td><span class="badge {{ $jadwal->status_badge }}">{{ ucfirst($jadwal->status) }}</span></td></tr>
                    <tr><th>Petugas</th><td>{{ $jadwal->petugas->name ?? '-' }}</td></tr>
                </table>
                @if ($jadwal->keterangan)
                    <hr>
                    <small class="text-muted">{{ $jadwal->keterangan }}</small>
                @endif
            </div>
        </div>

        {{-- Statistik --}}
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <h4 class="mb-0 text-primary">{{ $stats['total'] }}</h4>
                        <small class="text-muted">Total</small>
                    </div>
                    <div class="col-4">
                        <h4 class="mb-0 text-info">{{ $stats['balita'] }}</h4>
                        <small class="text-muted">Balita</small>
                    </div>
                    <div class="col-4">
                        <h4 class="mb-0 text-warning">{{ $stats['lansia'] }}</h4>
                        <small class="text-muted">Lansia</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong>Daftar Peserta ({{ $jadwal->pemeriksaans->count() }})</strong></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>BB / TB</th>
                            <th>Tekanan Darah</th>
                            <th>Status Gizi</th>
                            <th width="80" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jadwal->pemeriksaans as $p)
                        <tr>
                            <td><strong>{{ $p->warga->nama ?? '-' }}</strong><br><small class="text-muted">{{ $p->warga->umur ?? '-' }} th</small></td>
                            <td><span class="badge bg-secondary">{{ $p->kategori_label }}</span></td>
                            <td>
                                @if ($p->berat_badan)
                                    {{ $p->berat_badan }} kg / {{ $p->tinggi_badan }} cm
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($p->tekanan_sistolik)
                                    {{ $p->tekanan_sistolik }}/{{ $p->tekanan_diastolik }} mmHg
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($p->status_gizi)
                                    <span class="badge {{ $p->status_gizi_badge }}">{{ $p->status_gizi_label }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('posyandu.pemeriksaan.show', $p) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-3 text-muted">Belum ada peserta diperiksa.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
@extends('layouts.admin')

@section('title', 'Manajemen Tamu')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">👥 Manajemen Tamu</h5>
        <small class="text-muted">Total: {{ $tamus->total() }} kunjungan</small>
    </div>
    <div>
        @can('tamu.create')
        <a href="{{ route('tamu.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Catat Tamu
        </a>
        @endcan
    </div>
</div>

{{-- Statistik --}}
<div class="row g-2 mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Total Kunjungan</small>
                <h5 class="mb-0">{{ $stats['total'] }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Sedang di Dalam</small>
                <h5 class="mb-0 text-warning">{{ $stats['sedang_di_dalam'] }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Hari Ini</small>
                <h5 class="mb-0 text-primary">{{ $stats['hari_ini'] }}</h5>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('tamu.index') }}" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="q" class="form-control form-control-sm" 
                       placeholder="Cari nama / kode / HP..." value="{{ request('q') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="masuk" {{ request('status') == 'masuk' ? 'selected' : '' }}>Masuk</option>
                    <option value="keluar" {{ request('status') == 'keluar' ? 'selected' : '' }}>Keluar</option>
                    <option value="tidak_kembali" {{ request('status') == 'tidak_kembali' ? 'selected' : '' }}>Tidak Kembali</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="tujuan_tipe" class="form-select form-select-sm">
                    <option value="">Semua Tujuan</option>
                    <option value="warga" {{ request('tujuan_tipe') == 'warga' ? 'selected' : '' }}>Warga</option>
                    <option value="rw" {{ request('tujuan_tipe') == 'rw' ? 'selected' : '' }}>RW</option>
                    <option value="rt" {{ request('tujuan_tipe') == 'rt' ? 'selected' : '' }}>RT</option>
                    <option value="umum" {{ request('tujuan_tipe') == 'umum' ? 'selected' : '' }}>Umum</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="dari" class="form-control form-control-sm" value="{{ request('dari') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i> Filter</button>
                <a href="{{ route('tamu.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i> Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th width="50">#</th>
                    <th>Kode</th>
                    <th>Nama Tamu</th>
                    <th>Tujuan</th>
                    <th>Keperluan</th>
                    <th>Waktu Masuk</th>
                    <th>Status</th>
                    <th width="140" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tamus as $i => $t)
                <tr>
                    <td>{{ $tamus->firstItem() + $i }}</td>
                    <td><code>{{ $t->kode_tamu }}</code></td>
                    <td>
                        <strong>{{ $t->nama }}</strong>
                        @if ($t->no_hp)
                            <br><small class="text-muted">{{ $t->no_hp }}</small>
                        @endif
                        @if ($t->instansi)
                            <br><small class="text-muted">{{ $t->instansi }}</small>
                        @endif
                    </td>
                    <td><small>{{ $t->tujuan_label }}</small></td>
                    <td><small>{{ Str::limit($t->keperluan, 50) }}</small></td>
                    <td>
                        <small>{{ $t->waktu_masuk->format('d M Y H:i') }}</small>
                        @if ($t->waktu_keluar)
                            <br><small class="text-success">Keluar: {{ $t->waktu_keluar->format('H:i') }}</small>
                        @endif
                        @if ($t->catatan_keluar)
                            <br><small class="text-muted fst-italic" title="{{ $t->catatan_keluar }}">
                                <i class="bi bi-chat-left-text"></i> {{ Str::limit($t->catatan_keluar, 30) }}
                            </small>
                        @endif
                    </td>
                    <td><span class="badge {{ $t->status_badge }}">{{ ucfirst(str_replace('_', ' ', $t->status)) }}</span></td>
                    <td class="text-center">
                        {{-- Tombol Detail --}}
                        <a href="{{ route('tamu.show', $t) }}" class="btn btn-sm btn-outline-info" title="Detail">
                            <i class="bi bi-eye"></i>
                        </a>

                        {{-- Tombol Edit (Super Admin, Ketua RW, Ketua RT, Security) --}}
                        @if(auth()->user()->hasAnyRole(['super_admin', 'ketua_rw', 'ketua_rt', 'security']))
                            <a href="{{ route('tamu.edit', $t) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                        @endif

                        {{-- Tombol Checkout (Super Admin, Ketua RW, Ketua RT, Security) --}}
                        @if(auth()->user()->hasAnyRole(['super_admin', 'ketua_rw', 'ketua_rt', 'security']))
                            @if ($t->status === 'masuk')
                                <button type="button" class="btn btn-sm btn-outline-success"
                                        data-bs-toggle="modal" data-bs-target="#checkoutModal{{ $t->id }}"
                                        title="Checkout">
                                    <i class="bi bi-box-arrow-right"></i>
                                </button>
                            @endif
                        @endif
                    </td>
                </tr>

                {{-- Modal Checkout (dipindah ke luar <tr> agar tidak merusak tabel) --}}
                @if ($t->status === 'masuk' && auth()->user()->hasAnyRole(['super_admin', 'ketua_rw', 'ketua_rt', 'security']))
                <div class="modal fade" id="checkoutModal{{ $t->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('tamu.checkout', $t) }}">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">Check-out Tamu: {{ $t->nama }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            Masuk: {{ $t->waktu_masuk->format('d M Y H:i') }} |
                                            Durasi: {{ $t->durasi_kunjungan }}
                                        </small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Catatan Kegiatan / Keperluan <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="catatan_keluar" class="form-control" rows="3"
                                                  placeholder="Contoh: Sudah selesai bertemu Pak RT, urusan surat domisili."
                                                  required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle"></i> Konfirmasi Keluar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox" style="font-size: 40px;"></i>
                        <p class="mb-0 mt-2">Belum ada tamu.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($tamus->hasPages())
    <div class="card-footer bg-white">{{ $tamus->links() }}</div>
    @endif
</div>

@endsection
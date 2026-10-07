{{-- Header + Tombol Buat --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Jadwal Posyandu</h5>
        <small class="text-muted">Total: {{ $jadwals->total() }} jadwal</small>
    </div>
    @can('posyandu.create')
    <a href="{{ route('posyandu.jadwal.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Buat Jadwal
    </a>
    @endcan
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('posyandu.index') }}" class="row g-2">
            {{-- Pertahankan tab aktif --}}
            <input type="hidden" name="tab" value="jadwal">

            <div class="col-md-4">
                <select name="jenis" class="form-select form-select-sm">
                    <option value="">Semua Jenis</option>
                    <option value="balita" {{ request('jenis') == 'balita' ? 'selected' : '' }}>Balita</option>
                    <option value="lansia" {{ request('jenis') == 'lansia' ? 'selected' : '' }}>Lansia</option>
                    <option value="umum" {{ request('jenis') == 'umum' ? 'selected' : '' }}>Umum</option>
                </select>
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach (['rencana' => 'Rencana', 'berlangsung' => 'Berlangsung', 'selesai' => 'Selesai', 'batal' => 'Batal'] as $v => $l)
                        <option value="{{ $v }}" {{ request('status') == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('posyandu.index', ['tab' => 'jadwal']) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </a>
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
                    <th>Nama Jadwal</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Jenis</th>
                    <th>Lokasi</th>
                    <th class="text-center">Peserta</th>
                    <th>Status</th>
                    <th width="100" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($jadwals as $i => $j)
                <tr>
                    <td>{{ $jadwals->firstItem() + $i }}</td>
                    <td><strong>{{ $j->nama }}</strong></td>
                    <td>{{ $j->tanggal->format('d M Y') }}</td>
                    <td>
                        @if ($j->jam_mulai)
                            <small>{{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}</small>
                        @else
                            -
                        @endif
                    </td>
                    <td><span class="badge bg-info text-dark">{{ $j->jenis_label }}</span></td>
                    <td><small>{{ $j->lokasi ?? '-' }}</small></td>
                    <td class="text-center"><span class="badge bg-primary">{{ $j->pemeriksaans_count }}</span></td>
                    <td><span class="badge {{ $j->status_badge }}">{{ ucfirst($j->status) }}</span></td>
                    <td class="text-center">
                        <a href="{{ route('posyandu.jadwal.show', $j) }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox" style="font-size: 40px;"></i>
                        <p class="mb-0 mt-2">Belum ada jadwal Posyandu.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($jadwals->hasPages())
    <div class="card-footer bg-white">{{ $jadwals->links() }}</div>
    @endif
</div>
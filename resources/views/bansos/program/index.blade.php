{{-- Header + Tombol Buat --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Program Bantuan Sosial</h5>
        <small class="text-muted">Total: {{ $programs->total() }} program</small>
    </div>
    @can('bansos.create')
    <a href="{{ route('bansos.program.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Buat Program
    </a>
    @endcan
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('bansos.index') }}" class="row g-2">
            {{-- Pertahankan tab aktif --}}
            <input type="hidden" name="tab" value="program">

            <div class="col-md-4">
                <input type="text" name="q" class="form-control form-control-sm" 
                       placeholder="Cari nama program..." value="{{ request('q') }}">
            </div>
            <div class="col-md-3">
                <select name="kategori" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    @foreach (['blt' => 'BLT', 'pkh' => 'PKH', 'bpnt' => 'BPNT', 'sembako' => 'Sembako', 'kesehatan' => 'Kesehatan', 'pendidikan' => 'Pendidikan', 'bencana' => 'Bencana', 'lainnya' => 'Lainnya'] as $v => $l)
                        <option value="{{ $v }}" {{ request('kategori') == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach (['draft' => 'Draft', 'aktif' => 'Aktif', 'selesai' => 'Selesai', 'batal' => 'Batal'] as $v => $l)
                        <option value="{{ $v }}" {{ request('status') == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('bansos.index', ['tab' => 'program']) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i>
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
                    <th>Kode</th>
                    <th>Nama Program</th>
                    <th>Kategori</th>
                    <th>Nilai Bantuan</th>
                    <th>Periode</th>
                    <th class="text-center">Penerima</th>
                    <th>Status</th>
                    <th width="100" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($programs as $i => $p)
                <tr>
                    <td>{{ $programs->firstItem() + $i }}</td>
                    <td><code>{{ $p->kode_program }}</code></td>
                    <td>
                        <a href="{{ route('bansos.program.show', $p) }}" class="text-decoration-none">
                            <strong>{{ $p->nama }}</strong>
                        </a>
                        @if ($p->sumber)
                            <br><small class="text-muted">{{ $p->sumber }}</small>
                        @endif
                    </td>
                    <td><span class="badge bg-info text-dark">{{ ucfirst($p->kategori) }}</span></td>
                    <td>
                        @if ($p->nominal)
                            <strong>Rp {{ number_format($p->nominal, 0, ',', '.') }}</strong>
                            <br><small class="text-muted">{{ $p->satuan_bantuan ?? '' }}</small>
                        @else
                            {{ $p->jenis_bantuan_label }}
                        @endif
                    </td>
                    <td>
                        <small>{{ $p->tanggal_mulai->format('d M Y') }}</small>
                        @if ($p->tanggal_selesai)
                            <br><small class="text-muted">s/d {{ $p->tanggal_selesai->format('d M Y') }}</small>
                        @endif
                    </td>
                    <td class="text-center"><span class="badge bg-primary">{{ $p->penerimas_count }}</span></td>
                    <td><span class="badge {{ $p->status_badge }}">{{ ucfirst($p->status) }}</span></td>
                    <td class="text-center">
                        <a href="{{ route('bansos.program.show', $p) }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox" style="font-size: 40px;"></i>
                        <p class="mb-0 mt-2">Belum ada program bansos.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($programs->hasPages())
    <div class="card-footer bg-white">{{ $programs->links() }}</div>
    @endif
</div>
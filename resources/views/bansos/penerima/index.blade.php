{{-- Header + Tombol Tambah --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">👥 Penerima Bantuan Sosial</h5>
        <small class="text-muted">Total: {{ $penerimas->total() }} penerima</small>
    </div>
    @can('bansos.create')
    <a href="{{ route('bansos.penerima.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Penerima
    </a>
    @endcan
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('bansos.index') }}" class="row g-2">
            {{-- Pertahankan tab aktif --}}
            <input type="hidden" name="tab" value="penerima">

            <div class="col-md-3">
                <input type="text" name="q" class="form-control form-control-sm" 
                       placeholder="Cari nama penerima..." value="{{ request('q') }}">
            </div>
            <div class="col-md-3">
                <select name="program_id" class="form-select form-select-sm">
                    <option value="">Semua Program</option>
                    @foreach ($programList as $p)
                        <option value="{{ $p->id }}" {{ request('program_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status_kelayakan" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status_kelayakan') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="layak" {{ request('status_kelayakan') == 'layak' ? 'selected' : '' }}>Layak</option>
                    <option value="tidak_layak" {{ request('status_kelayakan') == 'tidak_layak' ? 'selected' : '' }}>Tidak Layak</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('bansos.index', ['tab' => 'penerima']) }}" class="btn btn-sm btn-outline-secondary">
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
                    <th>Nama Penerima</th>
                    <th>Program</th>
                    <th>RT</th>
                    <th>Skor</th>
                    <th>Status Kelayakan</th>
                    <th>Diterima</th>
                    <th width="100" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($penerimas as $i => $p)
                <tr>
                    <td>{{ $penerimas->firstItem() + $i }}</td>
                    <td>
                        <strong>{{ $p->nama_penerima }}</strong>
                        <br><small class="text-muted">{{ $p->nik_penerima }}</small>
                    </td>
                    <td>
                        <small>{{ $p->program->nama ?? '-' }}</small>
                        <br><small class="badge bg-info text-dark">{{ $p->program->kategori ?? '-' }}</small>
                    </td>
                    <td>{{ $p->rt->nama_rt ?? '-' }}</td>
                    <td>{{ $p->skor_kelayakan }}</td>
                    <td><span class="badge {{ $p->status_kelayakan_badge }}">{{ $p->status_kelayakan_label }}</span></td>
                    <td>
                        @if ($p->total_diterima > 0)
                            <strong class="text-success">Rp {{ number_format($p->total_diterima, 0, ',', '.') }}</strong>
                        @else
                            <small class="text-muted">-</small>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('bansos.penerima.show', $p) }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox" style="font-size: 40px;"></i>
                        <p class="mb-0 mt-2">Belum ada penerima bansos.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($penerimas->hasPages())
    <div class="card-footer bg-white">{{ $penerimas->links() }}</div>
    @endif
</div>
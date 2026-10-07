{{-- Header + Tombol Catat --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Penyaluran Bantuan Sosial</h5>
        <small class="text-muted">Total: {{ $penyalurans->total() }} penyaluran</small>
    </div>
    @can('bansos.salurkan')
    <a href="{{ route('bansos.penyaluran.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Catat Penyaluran
    </a>
    @endcan
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('bansos.index') }}" class="row g-2">
            {{-- Pertahankan tab aktif --}}
            <input type="hidden" name="tab" value="penyaluran">

            <div class="col-md-3">
                <select name="program_id" class="form-select form-select-sm">
                    <option value="">Semua Program</option>
                    @foreach ($programList as $p)
                        <option value="{{ $p->id }}" {{ request('program_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="dijadwalkan" {{ request('status') == 'dijadwalkan' ? 'selected' : '' }}>Dijadwalkan</option>
                    <option value="disalurkan" {{ request('status') == 'disalurkan' ? 'selected' : '' }}>Disalurkan</option>
                    <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="dari" class="form-control form-control-sm" value="{{ request('dari') }}" placeholder="Dari tanggal">
            </div>
            <div class="col-md-2">
                <input type="date" name="sampai" class="form-control form-control-sm" value="{{ request('sampai') }}" placeholder="Sampai tanggal">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('bansos.index', ['tab' => 'penyaluran']) }}" class="btn btn-sm btn-outline-secondary">
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
                    <th>Penerima</th>
                    <th>Program</th>
                    <th>Tanggal</th>
                    <th>Nominal</th>
                    <th>Status</th>
                    <th width="80" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($penyalurans as $i => $s)
                <tr>
                    <td>{{ $penyalurans->firstItem() + $i }}</td>
                    <td><code>{{ $s->kode_penyaluran }}</code></td>
                    <td>
                        <strong>{{ $s->penerima->nama_penerima ?? '-' }}</strong>
                        <br><small class="text-muted">{{ $s->penerima->rt->nama_rt ?? '-' }}</small>
                    </td>
                    <td><small>{{ $s->program->nama ?? '-' }}</small></td>
                    <td><small>{{ $s->tanggal->format('d M Y') }}</small></td>
                    <td class="text-success"><strong>Rp {{ number_format($s->nominal, 0, ',', '.') }}</strong></td>
                    <td><span class="badge {{ $s->status_badge }}">{{ ucfirst($s->status) }}</span></td>
                    <td class="text-center">
                        <a href="{{ route('bansos.penyaluran.show', $s) }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox" style="font-size: 40px;"></i>
                        <p class="mb-0 mt-2">Belum ada penyaluran.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($penyalurans->hasPages())
    <div class="card-footer bg-white">{{ $penyalurans->links() }}</div>
    @endif
</div>
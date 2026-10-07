@extends('layouts.admin')

@section('title', 'Jenis Iuran')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Jenis Iuran</h5>
        <small class="text-muted">Total: {{ $iurans->total() }} jenis iuran</small>
    </div>
    @can('iuran.create')
    <a href="{{ route('keuangan.iuran.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Iuran
    </a>
    @endcan
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th width="50">#</th>
                    <th>Nama Iuran</th>
                    <th>Kategori</th>
                    <th>Nominal</th>
                    <th>Periode</th>
                    <th>Berlaku</th>
                    <th class="text-center">Tagihan</th>
                    <th>Status</th>
                    <th width="150" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($iurans as $i => $iuran)
                <tr>
                    <td>{{ $iurans->firstItem() + $i }}</td>
                    <td>
                        <strong>{{ $iuran->nama }}</strong>
                        @if ($iuran->keterangan)
                            <br><small class="text-muted">{{ Str::limit($iuran->keterangan, 50) }}</small>
                        @endif
                    </td>
                    <td><span class="badge bg-info text-dark">{{ ucfirst($iuran->kategori) }}</span></td>
                    <td><strong>Rp {{ number_format($iuran->nominal_default, 0, ',', '.') }}</strong></td>
                    <td>{{ $iuran->periode_label }}</td>
                    <td>
                        @if ($iuran->rt)
                            <span class="badge bg-secondary">{{ $iuran->rt->nama_rt }}</span>
                        @else
                            <span class="badge bg-primary">Semua RT</span>
                        @endif
                    </td>
                    <td class="text-center"><span class="badge bg-light text-dark">{{ $iuran->tagihans_count }}</span></td>
                    <td>
                        @if ($iuran->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @can('tagihan.generate')
                        <a href="{{ route('keuangan.tagihan.create', ['iuran_id' => $iuran->id]) }}" 
                           class="btn btn-sm btn-outline-primary" title="Generate Tagihan">
                            <i class="bi bi-magic"></i>
                        </a>
                        @endcan
                        @can('iuran.edit')
                        <a href="{{ route('keuangan.iuran.edit', $iuran) }}" class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @endcan
                        @can('iuran.delete')
                        <form method="POST" action="{{ route('keuangan.iuran.destroy', $iuran) }}" class="d-inline"
                              onsubmit="return confirm('Yakin hapus iuran ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox" style="font-size: 40px;"></i>
                        <p class="mb-0 mt-2">Belum ada jenis iuran.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($iurans->hasPages())
    <div class="card-footer bg-white">{{ $iurans->links() }}</div>
    @endif
</div>

@endsection
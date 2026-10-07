@extends('layouts.admin')

@section('title', 'Bantuan Sosial')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Bantuan Sosial</h5>
        <small class="text-muted">Kelola program, penerima, dan penyaluran bantuan</small>
    </div>
</div>

{{-- Statistik Global --}}
<div class="row g-2 mb-3">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Total Program</small>
                <h5 class="mb-0">{{ $stats['total_program'] }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Program Aktif</small>
                <h5 class="mb-0 text-success">{{ $stats['program_aktif'] }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Penerima Layak</small>
                <h5 class="mb-0 text-primary">{{ $stats['penerima_layak'] }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Total Disalurkan</small>
                <h5 class="mb-0 text-info">Rp {{ number_format($stats['total_disalurkan'], 0, ',', '.') }}</h5>
            </div>
        </div>
    </div>
</div>

{{-- Tab Navigation --}}
<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'program' ? 'active' : '' }}"
           href="{{ route('bansos.index', ['tab' => 'program']) }}">
            <i class="bi bi-clipboard-list"></i> Program
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'penerima' ? 'active' : '' }}"
           href="{{ route('bansos.index', ['tab' => 'penerima']) }}">
            <i class="bi bi-people"></i> Penerima
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'penyaluran' ? 'active' : '' }}"
           href="{{ route('bansos.index', ['tab' => 'penyaluran']) }}">
            <i class="bi bi-box-seam"></i> Penyaluran
        </a>
    </li>
</ul>

{{-- Isi Tab --}}
<div class="tab-content">
    @if ($tab === 'program')
        @include('bansos.program.index')
    @elseif ($tab === 'penerima')
        @include('bansos.penerima.index')
    @elseif ($tab === 'penyaluran')
        @include('bansos.penyaluran.index')
    @endif
</div>

@endsection
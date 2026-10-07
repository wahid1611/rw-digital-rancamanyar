@extends('layouts.admin')

@section('title', 'Posyandu')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1">Posyandu</h5>
        <small class="text-muted">Kelola jadwal dan kegiatan Posyandu</small>
    </div>
</div>

{{-- Statistik Global --}}
<div class="row g-2 mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <small class="text-muted">Total Jadwal</small>
                <h5 class="mb-0">{{ $stats['total_jadwal'] }}</h5>
            </div>
        </div>
    </div>
</div>

{{-- Tab Navigation --}}
<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'jadwal' ? 'active' : '' }}"
           href="{{ route('posyandu.index', ['tab' => 'jadwal']) }}">
            <i class="bi bi-calendar-event"></i> Jadwal Posyandu
        </a>
    </li>
</ul>

{{-- Isi Tab --}}
<div class="tab-content">
    @if ($tab === 'jadwal')
        @include('posyandu.jadwal.index')
    @endif
</div>

@endsection
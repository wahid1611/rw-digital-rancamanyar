@extends('layouts.admin')

@section('title', 'Data Keluarga Tidak Ditemukan')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center p-5">
                <i class="bi bi-exclamation-triangle text-warning" style="font-size: 80px;"></i>
                <h4 class="mt-3">Data Keluarga Tidak Ditemukan</h4>
                <p class="text-muted">
                    Akun Anda belum terhubung dengan data keluarga (KK). 
                    Silakan hubungi <strong>Ketua RT</strong> atau <strong>Sekretaris RW</strong> 
                    untuk menghubungkan akun Anda dengan data keluarga.
                </p>

                <div class="alert alert-info text-start mt-3">
                    <strong>Yang perlu dilakukan:</strong>
                    <ol class="mb-0 mt-2">
                        <li>Lapor ke Ketua RT bahwa akun Anda belum terhubung ke KK</li>
                        <li>Ketua RT akan menghubungi Sekretaris RW</li>
                        <li>Sekretaris akan menghubungkan akun Anda ke data keluarga</li>
                        <li>Setelah itu, Anda bisa lihat tagihan iuran</li>
                    </ol>
                </div>

                <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-house"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
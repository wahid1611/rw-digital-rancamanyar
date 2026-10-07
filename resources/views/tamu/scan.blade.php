@extends('layouts.admin')

@section('title', 'Scan QR Tamu')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-1">Scan QR Tamu</h5>
    <a href="{{ route('tamu.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="alert alert-info">
    <i class="bi bi-info-circle"></i>
    Scan QR code tamu untuk check-out cepat. Atau masukkan kode QR manual di bawah.
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
</div>
@endif

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('tamu.proses-scan') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Kode QR Tamu <span class="text-danger">*</span></label>
                        <input type="text" name="qr_code" class="form-control form-control-lg" 
                               placeholder="Masukkan kode QR..." autofocus required
                               style="text-align: center; font-family: monospace; letter-spacing: 2px;">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                        <i class="bi bi-search"></i> Cari Tamu
                    </button>
                </form>

                <hr class="my-4">

                <p class="text-center text-muted small mb-3">
                    Atau gunakan kamera untuk scan QR
                </p>
                <button type="button" id="start-camera" class="btn btn-outline-primary w-100">
                    <i class="bi bi-camera-video"></i> Aktifkan Kamera
                </button>

                <div id="reader" class="mt-3" style="width: 100%;"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let scanner = null;

    document.getElementById('start-camera').addEventListener('click', function() {
        this.style.display = 'none';

        scanner = new Html5QrcodeScanner("reader", { 
            fps: 10, 
            qrbox: 250 
        });

        scanner.render(function(decodedText) {
            // Isi form dengan hasil scan
            document.querySelector('input[name="qr_code"]').value = decodedText;
            
            // Auto submit
            document.querySelector('form').submit();
        });
    });
</script>
@endpush

@endsection
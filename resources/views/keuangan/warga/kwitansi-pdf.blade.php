<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi {{ $pembayaran->kode_pembayaran }}</title>
    <style>
        @page { margin: 1cm; }
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            color: #000;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 { font-size: 16pt; margin: 0; }
        .header h2 { font-size: 14pt; margin: 3px 0; }
        .header p { font-size: 10pt; margin: 2px 0; }
        .title {
            text-align: center;
            margin: 20px 0;
        }
        .title h3 {
            font-size: 16pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
        }
        .content { padding: 0 20px; }
        .row {
            display: flex;
            margin-bottom: 8px;
        }
        .label {
            width: 200px;
            font-weight: bold;
        }
        .colon { width: 20px; }
        .value { flex: 1; }
        .nominal {
            font-size: 18pt;
            font-weight: bold;
            text-align: center;
            padding: 15px;
            border: 2px solid #000;
            margin: 20px 0;
        }
        .ttd {
            float: right;
            text-align: center;
            margin-top: 30px;
            width: 250px;
        }
        .ttd .space { height: 70px; }
        .ttd .nama {
            font-weight: bold;
            text-decoration: underline;
        }
        .clear { clear: both; }
        .footer {
            margin-top: 30px;
            font-size: 9pt;
            color: #666;
            font-style: italic;
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- KOP --}}
    <div class="header">
        <h1>PENGURUS RUKUN WARGA 07</h1>
        <h2>PERUMAHAN RANCAMANYAR</h2>
        <p>Desa Wancimekar, Kecamatan Kotabaru, Kabupaten Karawang</p>
        <p>Jawa Barat — 41374</p>
    </div>

    {{-- JUDUL --}}
    <div class="title">
        <h3>KWITANSI PEMBAYARAN</h3>
        <p>No: {{ $pembayaran->kode_pembayaran }}</p>
    </div>

    {{-- ISI --}}
    <div class="content">
        <div class="row">
            <div class="label">Telah diterima dari</div>
            <div class="colon">:</div>
            <div class="value"><strong>{{ $pembayaran->tagihan->keluarga->kepala_keluarga_nama ?? '-' }}</strong></div>
        </div>
        <div class="row">
            <div class="label">No. KK</div>
            <div class="colon">:</div>
            <div class="value">{{ $pembayaran->tagihan->keluarga->no_kk ?? '-' }}</div>
        </div>
        <div class="row">
            <div class="label">RT</div>
            <div class="colon">:</div>
            <div class="value">{{ $pembayaran->tagihan->rt->nama_rt ?? '-' }}</div>
        </div>
        <div class="row">
            <div class="label">Untuk Pembayaran</div>
            <div class="colon">:</div>
            <div class="value">
                {{ $pembayaran->tagihan->iuran->nama ?? '-' }} 
                (Periode {{ $pembayaran->tagihan->periode }})
            </div>
        </div>
        <div class="row">
            <div class="label">Metode</div>
            <div class="colon">:</div>
            <div class="value">{{ $pembayaran->metode_label }}</div>
        </div>
        <div class="row">
            <div class="label">Tanggal Bayar</div>
            <div class="colon">:</div>
            <div class="value">{{ $pembayaran->tgl_bayar->format('d F Y') }}</div>
        </div>

        <div class="nominal">
            Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}
        </div>

        <div class="ttd">
            <p>Wancimekar, {{ $pembayaran->tgl_bayar->format('d F Y') }}</p>
            <p>Bendahara RW 07</p>
            <div class="space"></div>
            <p class="nama">{{ $pembayaran->pencatat->name ?? '..............................' }}</p>
        </div>
        <div class="clear"></div>
    </div>

    <div class="footer">
        Kwitansi ini diterbitkan secara digital melalui Sistem Informasi RW Digital.<br>
        Kode: {{ $pembayaran->kode_pembayaran }} • Diterbitkan: {{ now()->format('d F Y H:i') }}
    </div>

</body>
</html>
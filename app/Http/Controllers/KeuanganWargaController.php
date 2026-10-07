<?php

namespace App\Http\Controllers;

use App\Models\KeuanganKas;
use App\Models\KeuanganPembayaran;
use App\Models\KeuanganTagihan;
use App\Models\Rt;
use Illuminate\Http\Request;

class KeuanganWargaController extends Controller
{
    /**
     * Halaman "Tagihan Saya" — warga lihat tagihan sendiri
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Cek user punya keluarga
        if (!$user->keluarga_id) {
            return view('keuangan.warga.no-keluarga');
        }

        $keluargaId = $user->keluarga_id;

        // Filter periode
        $periode = $request->get('periode');
        $status = $request->get('status');

        // Query tagihan
        $query = KeuanganTagihan::with(['iuran', 'rt', 'pembayarans'])
            ->where('keluarga_id', $keluargaId)
            ->orderBy('jatuh_tempo', 'desc');

        if ($periode) $query->where('periode', $periode);
        if ($status) $query->where('status', $status);

        $tagihans = $query->paginate(20);

        // Statistik
        $totalTagihan = KeuanganTagihan::where('keluarga_id', $keluargaId)->count();
        $lunas = KeuanganTagihan::where('keluarga_id', $keluargaId)->where('status', 'lunas')->count();
        $belumBayar = KeuanganTagihan::where('keluarga_id', $keluargaId)
            ->whereIn('status', ['belum_bayar', 'sebagian', 'telat'])
            ->count();
        $totalTunggakan = KeuanganTagihan::where('keluarga_id', $keluargaId)
            ->whereIn('status', ['belum_bayar', 'sebagian', 'telat'])
            ->sum('tunggakan');

        // Info pembayaran RW
        $rw = \App\Models\Rw::first();

        // Daftar periode untuk filter
        $periodes = KeuanganTagihan::where('keluarga_id', $keluargaId)
            ->distinct()
            ->orderBy('periode', 'desc')
            ->pluck('periode');

        return view('keuangan.warga.index', compact(
            'tagihans', 'totalTagihan', 'lunas', 'belumBayar', 'totalTunggakan',
            'rw', 'periodes', 'periode', 'status'
        ));
    }

    /**
     * Halaman "Grafik Keuangan RW" — transparansi
     */
    public function grafik()
    {
        // Saldo kas RW (yang bisa dilihat publik)
        $saldoRw = KeuanganKas::saldoRw();
        $pemasukanBulanIni = KeuanganKas::pemilikRw()->where('jenis', 'pemasukan')
            ->whereMonth('tgl_transaksi', now()->month)
            ->whereYear('tgl_transaksi', now()->year)
            ->sum('nominal');
        $pengeluaranBulanIni = KeuanganKas::pemilikRw()->where('jenis', 'pengeluaran')
            ->whereMonth('tgl_transaksi', now()->month)
            ->whereYear('tgl_transaksi', now()->year)
            ->sum('nominal');

        // Pengeluaran per kategori (bulan ini)
        $pengeluaranKategori = KeuanganKas::pemilikRw()
            ->where('jenis', 'pengeluaran')
            ->whereMonth('tgl_transaksi', now()->month)
            ->whereYear('tgl_transaksi', now()->year)
            ->selectRaw('kategori, sum(nominal) as total')
            ->groupBy('kategori')
            ->pluck('total', 'kategori');

        // Pemasukan per kategori (bulan ini)
        $pemasukanKategori = KeuanganKas::pemilikRw()
            ->where('jenis', 'pemasukan')
            ->whereMonth('tgl_transaksi', now()->month)
            ->whereYear('tgl_transaksi', now()->year)
            ->selectRaw('kategori, sum(nominal) as total')
            ->groupBy('kategori')
            ->pluck('total', 'kategori');

        // Grafik 6 bulan terakhir
        $grafik = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $grafik[] = [
                'bulan' => $date->translatedFormat('M Y'),
                'masuk' => KeuanganKas::pemilikRw()->where('jenis', 'pemasukan')
                    ->whereMonth('tgl_transaksi', $date->month)
                    ->whereYear('tgl_transaksi', $date->year)
                    ->sum('nominal'),
                'keluar' => KeuanganKas::pemilikRw()->where('jenis', 'pengeluaran')
                    ->whereMonth('tgl_transaksi', $date->month)
                    ->whereYear('tgl_transaksi', $date->year)
                    ->sum('nominal'),
            ];
        }

        return view('keuangan.warga.grafik', compact(
            'saldoRw', 'pemasukanBulanIni', 'pengeluaranBulanIni',
            'pengeluaranKategori', 'pemasukanKategori', 'grafik'
        ));
    }

    /**
     * Detail Tagihan + Riwayat Pembayaran
     */
    public function show($id)
    {
        $user = auth()->user();

        $tagihan = KeuanganTagihan::with(['iuran', 'rt', 'pembayarans.pencatat'])
            ->where('keluarga_id', $user->keluarga_id)
            ->findOrFail($id);

        // Info pembayaran RW
        $rw = \App\Models\Rw::first();

        return view('keuangan.warga.show', compact('tagihan', 'rw'));
    }

    /**
     * Download Kwitansi (PDF)
     */
    public function kwitansi($pembayaranId)
    {
        $user = auth()->user();

        $pembayaran = KeuanganPembayaran::with(['tagihan.iuran', 'tagihan.keluarga', 'tagihan.rt', 'pencatat'])
            ->whereHas('tagihan', fn($q) => $q->where('keluarga_id', $user->keluarga_id))
            ->findOrFail($pembayaranId);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('keuangan.warga.kwitansi-pdf', [
        'pembayaran' => $pembayaran,
    ]);
        $pdf->setPaper('A5', 'landscape');

        return $pdf->download('kwitansi-' . $pembayaran->kode_pembayaran . '.pdf');
    }

    /**
     * Konfirmasi Bayar (warga upload bukti)
     */
    public function konfirmasiBayar(Request $request, $tagihanId)
    {
        $user = auth()->user();

        $tagihan = KeuanganTagihan::where('keluarga_id', $user->keluarga_id)
            ->findOrFail($tagihanId);

        if ($tagihan->status === 'lunas') {
            return back()->with('error', 'Tagihan ini sudah lunas.');
        }

        $validated = $request->validate([
            'nominal' => 'required|numeric|min:1',
            'metode' => 'required|in:tunai,transfer,qris',
            'tgl_bayar' => 'required|date',
            'no_referensi' => 'nullable|string|max:100',
            'bukti' => 'nullable|image|max:2048',
            'catatan' => 'nullable|string',
        ]);

        if ($request->hasFile('bukti')) {
            $validated['bukti'] = $request->file('bukti')->store('keuangan/bukti', 'public');
        }

        // Buat pembayaran dengan status 'pending' (nunggu verifikasi bendahara)
        $validated['tagihan_id'] = $tagihan->id;
        $validated['keluarga_id'] = $tagihan->keluarga_id;
        $validated['status'] = 'pending';
        $validated['dicatat_oleh'] = auth()->id();

        KeuanganPembayaran::create($validated);

        return redirect()->route('keuangan.warga.index')
            ->with('success', 'Konfirmasi pembayaran berhasil dikirim. Menunggu verifikasi Bendahara.');
    }
}
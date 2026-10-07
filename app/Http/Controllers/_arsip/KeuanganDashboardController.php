<?php

namespace App\Http\Controllers;

use App\Models\KeuanganKas;
use App\Models\KeuanganTagihan;
use App\Models\KeuanganPembayaran;
use App\Models\KeuanganIuran;

class KeuanganDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Base query: filter by RT kalau ketua RT
        $tagihanQuery = KeuanganTagihan::query();
        if ($user->hasRole('ketua_rt') && $user->rt_id && !$user->hasAnyRole(['super_admin', 'ketua_rw', 'bendahara'])) {
            $tagihanQuery->where('rt_id', $user->rt_id);
        }

        // Statistik utama
        $totalTagihan = (clone $tagihanQuery)->count();
        $belumBayar = (clone $tagihanQuery)->whereIn('status', ['belum_bayar', 'sebagian', 'telat'])->count();
        $lunas = (clone $tagihanQuery)->where('status', 'lunas')->count();
        $tunggakan = (clone $tagihanQuery)->whereIn('status', ['belum_bayar', 'sebagian', 'telat'])->sum('tunggakan');

        // Saldo kas
        $saldo = KeuanganKas::saldo();

        // Pemasukan bulan ini
        $pemasukanBulanIni = KeuanganKas::where('jenis', 'pemasukan')
            ->whereMonth('tgl_transaksi', now()->month)
            ->whereYear('tgl_transaksi', now()->year)
            ->sum('nominal');

        $pengeluaranBulanIni = KeuanganKas::where('jenis', 'pengeluaran')
            ->whereMonth('tgl_transaksi', now()->month)
            ->whereYear('tgl_transaksi', now()->year)
            ->sum('nominal');

        // Grafik 6 bulan terakhir
        $grafik = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $grafik[] = [
                'bulan' => $date->translatedFormat('M Y'),
                'masuk' => KeuanganKas::where('jenis', 'pemasukan')
                    ->whereMonth('tgl_transaksi', $date->month)
                    ->whereYear('tgl_transaksi', $date->year)
                    ->sum('nominal'),
                'keluar' => KeuanganKas::where('jenis', 'pengeluaran')
                    ->whereMonth('tgl_transaksi', $date->month)
                    ->whereYear('tgl_transaksi', $date->year)
                    ->sum('nominal'),
            ];
        }

        // Tunggakan per RT
        $tunggakanPerRt = KeuanganTagihan::whereIn('status', ['belum_bayar', 'sebagian', 'telat'])
            ->with('rt')
            ->selectRaw('rt_id, count(*) as jumlah, sum(tunggakan) as total')
            ->groupBy('rt_id')
            ->get();

        // Pembayaran terbaru
        $pembayaranTerbaru = KeuanganPembayaran::with(['keluarga', 'tagihan.iuran'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('keuangan.dashboard', compact(
            'totalTagihan', 'belumBayar', 'lunas', 'tunggakan', 'saldo',
            'pemasukanBulanIni', 'pengeluaranBulanIni',
            'grafik', 'tunggakanPerRt', 'pembayaranTerbaru'
        ));
    }
}
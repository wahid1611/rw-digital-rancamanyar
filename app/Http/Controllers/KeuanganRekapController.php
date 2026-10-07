<?php

namespace App\Http\Controllers;

use App\Models\KeuanganKas;
use App\Models\KeuanganTagihan;
use App\Models\Rt;
use Illuminate\Http\Request;
use App\Exports\RekapExport;
use Maatwebsite\Excel\Facades\Excel;

class KeuanganRekapController extends Controller
{
    /**
     * Dashboard Rekap — semua RT + RW
     */
    public function dashboard()
    {
        // Saldo RW
        $saldoRw = KeuanganKas::saldoRw();

        // Rekap per RT
        $rts = Rt::orderBy('nomor_rt')->get();

        $rekapRts = [];
        $totalSaldoRt = 0;
        $totalTunggakanRt = 0;

        foreach ($rts as $rt) {
            $saldo = KeuanganKas::saldoRt($rt->id);
            $pemasukan = KeuanganKas::pemilikRt($rt->id)->where('jenis', 'pemasukan')->sum('nominal');
            $pengeluaran = KeuanganKas::pemilikRt($rt->id)->where('jenis', 'pengeluaran')->sum('nominal');
            $tunggakan = KeuanganTagihan::pemilikRt($rt->id)
                ->whereIn('status', ['belum_bayar', 'sebagian', 'telat'])
                ->sum('tunggakan');
            $totalKK = KeuanganTagihan::pemilikRt($rt->id)->distinct('keluarga_id')->count('keluarga_id');
            $lunasKK = KeuanganTagihan::pemilikRt($rt->id)
                ->where('status', 'lunas')
                ->distinct('keluarga_id')
                ->count('keluarga_id');

            $rekapRts[] = [
                'rt' => $rt,
                'saldo' => $saldo,
                'pemasukan' => $pemasukan,
                'pengeluaran' => $pengeluaran,
                'tunggakan' => $tunggakan,
                'total_kk' => $totalKK,
                'lunas_kk' => $lunasKK,
            ];

            $totalSaldoRt += $saldo;
            $totalTunggakanRt += $tunggakan;
        }

        // Total keseluruhan
        $totalSemua = $saldoRw + $totalSaldoRt;

        // Statistik tambahan
        $totalTagihan = KeuanganTagihan::count();
        $belumBayar = KeuanganTagihan::whereIn('status', ['belum_bayar', 'sebagian', 'telat'])->count();

        return view('keuangan.rekap.dashboard', compact(
            'saldoRw', 'totalSaldoRt', 'totalSemua', 'rekapRts',
            'totalTunggakanRt', 'totalTagihan', 'belumBayar'
        ));
    }

    /**
     * Detail Rekap per RT
     */
    public function showRt($rtId)
    {
        $rt = Rt::findOrFail($rtId);

        // Data RT
        $saldo = KeuanganKas::saldoRt($rt->id);
        $pemasukan = KeuanganKas::pemilikRt($rt->id)->where('jenis', 'pemasukan')->sum('nominal');
        $pengeluaran = KeuanganKas::pemilikRt($rt->id)->where('jenis', 'pengeluaran')->sum('nominal');

        // Transaksi terbaru
        $transaksis = KeuanganKas::with('pencatat')
            ->pemilikRt($rt->id)
            ->orderBy('tgl_transaksi', 'desc')
            ->limit(20)
            ->get();

        // Tagihan belum bayar
        $belumBayar = KeuanganTagihan::with('keluarga')
            ->pemilikRt($rt->id)
            ->whereIn('status', ['belum_bayar', 'sebagian', 'telat'])
            ->orderBy('jatuh_tempo')
            ->get();

        return view('keuangan.rekap.detail-rt', compact(
            'rt', 'saldo', 'pemasukan', 'pengeluaran', 'transaksis', 'belumBayar'
        ));
    }

    /**
     * Laporan Gabungan (RW + Semua RT)
     */
    public function laporan(Request $request)
    {
        $dari = $request->get('dari', now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));

        // RW
        $transaksiRw = KeuanganKas::with('pencatat')
            ->pemilikRw()
            ->whereBetween('tgl_transaksi', [$dari, $sampai])
            ->orderBy('tgl_transaksi')
            ->get();

        $masukRw = $transaksiRw->where('jenis', 'pemasukan')->sum('nominal');
        $keluarRw = $transaksiRw->where('jenis', 'pengeluaran')->sum('nominal');

        // Per RT
        $rts = Rt::orderBy('nomor_rt')->get();
        $rekapRts = [];

        foreach ($rts as $rt) {
            $masuk = KeuanganKas::pemilikRt($rt->id)
                ->whereBetween('tgl_transaksi', [$dari, $sampai])
                ->where('jenis', 'pemasukan')
                ->sum('nominal');
            $keluar = KeuanganKas::pemilikRt($rt->id)
                ->whereBetween('tgl_transaksi', [$dari, $sampai])
                ->where('jenis', 'pengeluaran')
                ->sum('nominal');

            $rekapRts[] = [
                'rt' => $rt,
                'masuk' => $masuk,
                'keluar' => $keluar,
                'saldo' => $masuk - $keluar,
            ];
        }

        // Total keseluruhan
        $totalMasukRt = collect($rekapRts)->sum('masuk');
        $totalKeluarRt = collect($rekapRts)->sum('keluar');
        $totalMasuk = $masukRw + $totalMasukRt;
        $totalKeluar = $keluarRw + $totalKeluarRt;

        return view('keuangan.rekap.laporan', compact(
            'dari', 'sampai',
            'masukRw', 'keluarRw', 'transaksiRw',
            'rekapRts', 'totalMasukRt', 'totalKeluarRt',
            'totalMasuk', 'totalKeluar'
        ));
    }
    /**
 * Export Rekap ke Excel
 */
public function exportRekap()
{
    $filename = 'rekap-keuangan-semua-rt-' . now()->format('Y-m-d') . '.xlsx';
    return Excel::download(new RekapExport(), $filename);
}
}
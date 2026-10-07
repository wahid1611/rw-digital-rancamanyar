<?php

namespace App\Http\Controllers;

use App\Models\KeuanganIuran;
use App\Models\KeuanganTagihan;
use App\Models\KeuanganPembayaran;
use App\Models\KeuanganKas;
use App\Models\Keluarga;
use App\Models\Rt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Exports\KasRtExport;
use App\Exports\LaporanRtExport;
use Maatwebsite\Excel\Facades\Excel;

class KeuanganRtController extends Controller
{
    /**
     * Get RT user yang login (dengan validasi)
     */
    private function getUserRt()
    {
        $user = auth()->user();

        if (!$user->rt_id) {
            abort(403, 'Anda tidak terdaftar di RT manapun.');
        }

        return Rt::findOrFail($user->rt_id);
    }

    /**
     * Dashboard Keuangan RT
     */
    public function dashboard()
    {
        $rt = $this->getUserRt();

        $totalTagihan = KeuanganTagihan::pemilikRt($rt->id)->count();
        $belumBayar = KeuanganTagihan::pemilikRt($rt->id)->whereIn('status', ['belum_bayar', 'sebagian', 'telat'])->count();
        $lunas = KeuanganTagihan::pemilikRt($rt->id)->where('status', 'lunas')->count();
        $tunggakan = KeuanganTagihan::pemilikRt($rt->id)->whereIn('status', ['belum_bayar', 'sebagian', 'telat'])->sum('tunggakan');

        $saldo = KeuanganKas::saldoRt($rt->id);
        $pemasukanBulanIni = KeuanganKas::pemilikRt($rt->id)->where('jenis', 'pemasukan')
            ->whereMonth('tgl_transaksi', now()->month)
            ->whereYear('tgl_transaksi', now()->year)
            ->sum('nominal');
        $pengeluaranBulanIni = KeuanganKas::pemilikRt($rt->id)->where('jenis', 'pengeluaran')
            ->whereMonth('tgl_transaksi', now()->month)
            ->whereYear('tgl_transaksi', now()->year)
            ->sum('nominal');

        // Grafik 6 bulan
        $grafik = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $grafik[] = [
                'bulan' => $date->translatedFormat('M Y'),
                'masuk' => KeuanganKas::pemilikRt($rt->id)->where('jenis', 'pemasukan')
                    ->whereMonth('tgl_transaksi', $date->month)
                    ->whereYear('tgl_transaksi', $date->year)
                    ->sum('nominal'),
                'keluar' => KeuanganKas::pemilikRt($rt->id)->where('jenis', 'pengeluaran')
                    ->whereMonth('tgl_transaksi', $date->month)
                    ->whereYear('tgl_transaksi', $date->year)
                    ->sum('nominal'),
            ];
        }

        $pembayaranTerbaru = KeuanganPembayaran::with(['keluarga', 'tagihan.iuran'])
            ->whereHas('tagihan', fn($q) => $q->where('pemilik', 'rt')->where('rt_id', $rt->id))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('keuangan.rt.dashboard', compact(
            'rt', 'totalTagihan', 'belumBayar', 'lunas', 'tunggakan', 'saldo',
            'pemasukanBulanIni', 'pengeluaranBulanIni',
            'grafik', 'pembayaranTerbaru'
        ));
    }

    /**
     * Halaman Kas RT
     */
    public function kas(Request $request)
    {
        $rt = $this->getUserRt();
        $tab = $request->get('tab', 'masuk');

        $query = KeuanganKas::with(['pencatat'])
            ->pemilikRt($rt->id)
            ->orderBy('tgl_transaksi', 'desc');

        if ($tab === 'masuk') {
            $query->where('jenis', 'pemasukan');
        } elseif ($tab === 'keluar') {
            $query->where('jenis', 'pengeluaran');
        }

        $transaksis = $query->paginate(20);

        $totalMasuk = KeuanganKas::pemilikRt($rt->id)->where('jenis', 'pemasukan')->sum('nominal');
        $totalKeluar = KeuanganKas::pemilikRt($rt->id)->where('jenis', 'pengeluaran')->sum('nominal');
        $saldo = $totalMasuk - $totalKeluar;

        return view('keuangan.rt.kas', compact('rt', 'transaksis', 'tab', 'totalMasuk', 'totalKeluar', 'saldo'));
    }

    /**
     * Form catat uang masuk RT
     */
    public function createMasuk()
    {
        $rt = $this->getUserRt();

        // Ambil daftar iuran RW + RT untuk saran datalist
        $iurans = KeuanganIuran::where(function ($q) use ($rt) {
                $q->where('pemilik', 'rw')
                  ->orWhere(function ($sub) use ($rt) {
                      $sub->where('pemilik', 'rt')->where('pemilik_rt_id', $rt->id);
                  });
            })
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        $keluargas = Keluarga::where('rt_id', $rt->id)->orderBy('kepala_keluarga_nama')->get();

        return view('keuangan.rt.masuk-create', compact('rt', 'iurans', 'keluargas'));
    }

    /**
     * Simpan uang masuk RT
     */
    public function storeMasuk(Request $request)
    {
        $rt = $this->getUserRt();

        $validated = $request->validate([
            'keluarga_id' => 'nullable|exists:keluarga,id',
            'deskripsi_iuran' => 'nullable|string|max:150',
            'kategori' => 'required|string|max:50',
            'nominal' => 'required|numeric|min:1',
            'tgl_transaksi' => 'required|date',
            'deskripsi' => 'nullable|string',
            'bukti' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('bukti')) {
            $validated['bukti'] = $request->file('bukti')->store('keuangan/kas', 'public');
        }

        // Gabung deskripsi
        $descParts = [];
        if (!empty($validated['deskripsi_iuran'])) {
            $descParts[] = $validated['deskripsi_iuran'];
        }
        if (!empty($validated['deskripsi'])) {
            $descParts[] = $validated['deskripsi'];
        }
        $validated['deskripsi'] = implode(' — ', $descParts) ?: 'Uang masuk';
        unset($validated['deskripsi_iuran']);

        $validated['jenis'] = 'pemasukan';
        $validated['pemilik'] = 'rt';
        $validated['pemilik_rt_id'] = $rt->id;
        $validated['dicatat_oleh'] = auth()->id();

        KeuanganKas::create($validated);

        return redirect()->route('keuangan.rt.kas', ['tab' => 'masuk'])
            ->with('success', 'Uang masuk RT ' . $rt->nomor_rt . ' berhasil dicatat.');
    }

    /**
     * Form catat uang keluar RT
     */
    public function createKeluar()
    {
        $rt = $this->getUserRt();
        return view('keuangan.rt.keluar-create', compact('rt'));
    }

    /**
     * Simpan uang keluar RT
     */
    public function storeKeluar(Request $request)
    {
        $rt = $this->getUserRt();

        $validated = $request->validate([
            'kategori' => 'required|string|max:50',
            'nominal' => 'required|numeric|min:1',
            'tgl_transaksi' => 'required|date',
            'deskripsi' => 'required|string',
            'bukti' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('bukti')) {
            $validated['bukti'] = $request->file('bukti')->store('keuangan/kas', 'public');
        }

        $validated['jenis'] = 'pengeluaran';
        $validated['pemilik'] = 'rt';
        $validated['pemilik_rt_id'] = $rt->id;
        $validated['dicatat_oleh'] = auth()->id();

        KeuanganKas::create($validated);

        return redirect()->route('keuangan.rt.kas', ['tab' => 'keluar'])
            ->with('success', 'Uang keluar RT ' . $rt->nomor_rt . ' berhasil dicatat.');
    }

    /**
     * Lihat yang belum bayar (RT saja)
     */
    public function belumBayar(Request $request)
    {
        $rt = $this->getUserRt();
        $periode = $request->get('periode', now()->format('Y-m'));
        $iuranId = $request->get('iuran_id');

        $query = KeuanganTagihan::with(['keluarga', 'iuran'])
            ->pemilikRt($rt->id)
            ->where('periode', $periode)
            ->whereIn('status', ['belum_bayar', 'sebagian', 'telat']);

        if ($iuranId) {
            $query->where('iuran_id', $iuranId);
        }

        $belumBayars = $query->orderBy('keluarga_id')->paginate(30)->withQueryString();

        $iurans = KeuanganIuran::where(function ($q) use ($rt) {
                $q->where('pemilik', 'rw')
                  ->orWhere(function ($sub) use ($rt) {
                      $sub->where('pemilik', 'rt')->where('pemilik_rt_id', $rt->id);
                  });
            })
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        $totalKK = KeuanganTagihan::pemilikRt($rt->id)->where('periode', $periode)->distinct('keluarga_id')->count('keluarga_id');
        $totalBayar = KeuanganTagihan::pemilikRt($rt->id)->where('periode', $periode)->where('status', 'lunas')->distinct('keluarga_id')->count('keluarga_id');

        return view('keuangan.rt.belum-bayar', compact(
            'rt', 'belumBayars', 'iurans', 'periode', 'iuranId', 'totalKK', 'totalBayar'
        ));
    }

    /**
     * Laporan Keuangan RT
     */
    public function laporan(Request $request)
    {
        $rt = $this->getUserRt();
        $dari = $request->get('dari', now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));

        $transaksis = KeuanganKas::with(['pencatat'])
            ->pemilikRt($rt->id)
            ->whereBetween('tgl_transaksi', [$dari, $sampai])
            ->orderBy('tgl_transaksi')
            ->get();

        $totalMasuk = $transaksis->where('jenis', 'pemasukan')->sum('nominal');
        $totalKeluar = $transaksis->where('jenis', 'pengeluaran')->sum('nominal');
        $saldo = $totalMasuk - $totalKeluar;

        return view('keuangan.rt.laporan', compact(
            'rt', 'transaksis', 'dari', 'sampai', 'totalMasuk', 'totalKeluar', 'saldo'
        ));
    }
    /**
 * Export Kas RT ke Excel
 */
public function exportKas(Request $request)
{
    $rt = $this->getUserRt();
    $tab = $request->get('tab', 'masuk');
    $filename = 'kas-' . str_replace(' ', '-', strtolower($rt->nama_rt)) . '-' . $tab . '-' . now()->format('Y-m-d') . '.xlsx';
    return Excel::download(new KasRtExport($rt->id, $rt->nama_rt, $tab), $filename);
}

/**
 * Export Laporan RT ke Excel
 */
public function exportLaporan(Request $request)
{
    $rt = $this->getUserRt();
    $dari = $request->get('dari', now()->startOfMonth()->format('Y-m-d'));
    $sampai = $request->get('sampai', now()->format('Y-m-d'));
    $filename = 'laporan-' . str_replace(' ', '-', strtolower($rt->nama_rt)) . '-' . $dari . '.xlsx';
    return Excel::download(new LaporanRtExport($rt->id, $dari, $sampai), $filename);
}
}
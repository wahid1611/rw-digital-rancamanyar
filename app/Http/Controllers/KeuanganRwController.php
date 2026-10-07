<?php

namespace App\Http\Controllers;

use App\Models\KeuanganIuran;
use App\Models\KeuanganTagihan;
use App\Models\KeuanganPembayaran;
use App\Models\KeuanganKas;
use App\Models\Keluarga;
use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Exports\KasRwExport;
use App\Exports\LaporanRwExport;
use Maatwebsite\Excel\Facades\Excel;

class KeuanganRwController extends Controller
{
    /**
     * Dashboard Keuangan RW
     */
    public function dashboard()
    {
        // Statistik utama (RW saja)
        $totalTagihan = KeuanganTagihan::pemilikRw()->count();
        $belumBayar = KeuanganTagihan::pemilikRw()->whereIn('status', ['belum_bayar', 'sebagian', 'telat'])->count();
        $lunas = KeuanganTagihan::pemilikRw()->where('status', 'lunas')->count();
        $tunggakan = KeuanganTagihan::pemilikRw()->whereIn('status', ['belum_bayar', 'sebagian', 'telat'])->sum('tunggakan');

        $saldo = KeuanganKas::saldoRw();
        $pemasukanBulanIni = KeuanganKas::pemilikRw()->where('jenis', 'pemasukan')
            ->whereMonth('tgl_transaksi', now()->month)
            ->whereYear('tgl_transaksi', now()->year)
            ->sum('nominal');
        $pengeluaranBulanIni = KeuanganKas::pemilikRw()->where('jenis', 'pengeluaran')
            ->whereMonth('tgl_transaksi', now()->month)
            ->whereYear('tgl_transaksi', now()->year)
            ->sum('nominal');

        // Grafik 6 bulan
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

        // Pembayaran terbaru
        $pembayaranTerbaru = KeuanganPembayaran::with(['keluarga', 'tagihan.iuran'])
            ->whereHas('tagihan', fn($q) => $q->where('pemilik', 'rw'))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('keuangan.rw.dashboard', compact(
            'totalTagihan', 'belumBayar', 'lunas', 'tunggakan', 'saldo',
            'pemasukanBulanIni', 'pengeluaranBulanIni',
            'grafik', 'pembayaranTerbaru'
        ));
    }

    /**
     * Halaman Kas RW (3 tab)
     */
    public function kas(Request $request)
    {
        $tab = $request->get('tab', 'masuk');

        // Ambil transaksi
        $query = KeuanganKas::with(['pencatat'])
            ->where('pemilik', 'rw')
            ->orderBy('tgl_transaksi', 'desc');

        if ($tab === 'masuk') {
            $query->where('jenis', 'pemasukan');
        } elseif ($tab === 'keluar') {
            $query->where('jenis', 'pengeluaran');
        }

        $transaksis = $query->paginate(20);

        // Statistik
        $totalMasuk = KeuanganKas::pemilikRw()->where('jenis', 'pemasukan')->sum('nominal');
        $totalKeluar = KeuanganKas::pemilikRw()->where('jenis', 'pengeluaran')->sum('nominal');
        $saldo = $totalMasuk - $totalKeluar;

        return view('keuangan.rw.kas', compact('transaksis', 'tab', 'totalMasuk', 'totalKeluar', 'saldo'));
    }

    /**
     * Form catat uang masuk
     */
    public function createMasuk()
    {
        // Ambil daftar iuran untuk saran datalist
        $iurans = KeuanganIuran::pemilikRw()->where('is_active', true)->orderBy('nama')->get();
        $keluargas = Keluarga::orderBy('kepala_keluarga_nama')->get();

        return view('keuangan.rw.masuk-create', compact('iurans', 'keluargas'));
    }

    /**
     * Simpan uang masuk
     */
    public function storeMasuk(Request $request)
    {
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

        // Gabung deskripsi: "Iuran Sampah — Budi bayar"
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
        $validated['pemilik'] = 'rw';
        $validated['dicatat_oleh'] = auth()->id();

        KeuanganKas::create($validated);

        return redirect()->route('keuangan.rw.kas', ['tab' => 'masuk'])
            ->with('success', 'Uang masuk berhasil dicatat.');
    }

    /**
     * Form catat uang keluar
     */
    public function createKeluar()
    {
        return view('keuangan.rw.keluar-create');
    }

    /**
     * Simpan uang keluar
     */
    public function storeKeluar(Request $request)
    {
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
        $validated['pemilik'] = 'rw';
        $validated['dicatat_oleh'] = auth()->id();

        KeuanganKas::create($validated);

        return redirect()->route('keuangan.rw.kas', ['tab' => 'keluar'])
            ->with('success', 'Uang keluar berhasil dicatat.');
    }

    /**
     * Halaman "Belum Bayar" — lihat siapa yang belum bayar
     */
    public function belumBayar(Request $request)
    {
        $periode = $request->get('periode', now()->format('Y-m'));
        $iuranId = $request->get('iuran_id');

        // Ambil tagihan belum bayar
        $query = KeuanganTagihan::with(['keluarga', 'rt', 'iuran'])
            ->pemilikRw()
            ->where('periode', $periode)
            ->whereIn('status', ['belum_bayar', 'sebagian', 'telat']);

        if ($iuranId) {
            $query->where('iuran_id', $iuranId);
        }

        $belumBayars = $query->orderBy('rt_id')->paginate(30)->withQueryString();

        // Iuran untuk filter
        $iurans = KeuanganIuran::pemilikRw()->where('is_active', true)->orderBy('nama')->get();

        // Statistik
        $totalKK = KeuanganTagihan::pemilikRw()->where('periode', $periode)->distinct('keluarga_id')->count('keluarga_id');
        $totalBayar = KeuanganTagihan::pemilikRw()->where('periode', $periode)->where('status', 'lunas')->distinct('keluarga_id')->count('keluarga_id');

        return view('keuangan.rw.belum-bayar', compact(
            'belumBayars', 'iurans', 'periode', 'iuranId', 'totalKK', 'totalBayar'
        ));
    }

    /**
     * Laporan Keuangan RW
     */
    public function laporan(Request $request)
    {
        $dari = $request->get('dari', now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));

        $transaksis = KeuanganKas::with(['pencatat'])
            ->pemilikRw()
            ->whereBetween('tgl_transaksi', [$dari, $sampai])
            ->orderBy('tgl_transaksi')
            ->get();

        $totalMasuk = $transaksis->where('jenis', 'pemasukan')->sum('nominal');
        $totalKeluar = $transaksis->where('jenis', 'pengeluaran')->sum('nominal');
        $saldo = $totalMasuk - $totalKeluar;

        return view('keuangan.rw.laporan', compact(
            'transaksis', 'dari', 'sampai', 'totalMasuk', 'totalKeluar', 'saldo'
        ));
    }

    /**
     * Setting pembayaran RW (QRIS & Bank)
     */
    public function setting()
    {
        $rw = Rw::first();
        return view('keuangan.rw.setting', compact('rw'));
    }

    /**
     * Simpan setting pembayaran
     */
    public function storeSetting(Request $request)
    {
        $rw = Rw::first();

        $validated = $request->validate([
            'bank_nama' => 'nullable|string|max:100',
            'bank_rekening' => 'nullable|string|max:50',
            'bank_atas_nama' => 'nullable|string|max:100',
            'kontak_bendahara' => 'nullable|string|max:20',
            'foto_qris' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto_qris')) {
            if ($rw->foto_qris) Storage::disk('public')->delete($rw->foto_qris);
            $validated['foto_qris'] = $request->file('foto_qris')->store('rw/qris', 'public');
        }

        $rw->update($validated);

        return redirect()->route('keuangan.rw.setting')
            ->with('success', 'Pengaturan pembayaran berhasil disimpan.');
    }

    /**
     * Hapus foto QRIS
     */
    public function hapusQris()
    {
        $rw = Rw::first();

        if ($rw && $rw->foto_qris && Storage::disk('public')->exists($rw->foto_qris)) {
            Storage::disk('public')->delete($rw->foto_qris);
            $rw->update(['foto_qris' => null]);
        }

        return back()->with('success', 'Foto QRIS berhasil dihapus.');
    }

    /**
 * Export Kas RW ke Excel
 */
public function exportKas(Request $request)
{
    $tab = $request->get('tab', 'masuk');
    $filename = 'kas-rw-' . $tab . '-' . now()->format('Y-m-d') . '.xlsx';
    return Excel::download(new KasRwExport($tab), $filename);
}

/**
 * Export Laporan RW ke Excel
 */
public function exportLaporan(Request $request)
{
    $dari = $request->get('dari', now()->startOfMonth()->format('Y-m-d'));
    $sampai = $request->get('sampai', now()->format('Y-m-d'));
    $filename = 'laporan-rw-' . $dari . '-sd-' . $sampai . '.xlsx';
    return Excel::download(new LaporanRwExport($dari, $sampai), $filename);
}
}
<?php

namespace App\Http\Controllers;

use App\Models\KeuanganPembayaran;
use App\Models\KeuanganTagihan;
use App\Models\KeuanganKas;
use App\Models\Keluarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KeuanganPembayaranController extends Controller
{
    public function index(Request $request)
    {
        $query = KeuanganPembayaran::with(['tagihan.iuran', 'keluarga', 'pencatat']);
        $user = auth()->user();

        if ($user->hasRole('warga') && !$user->hasAnyRole(['super_admin', 'ketua_rw', 'ketua_rt', 'bendahara', 'sekretaris'])) {
            $query->where('keluarga_id', $user->keluarga_id ?? 0);
        }

        if ($request->filled('metode')) $query->where('metode', $request->metode);
        if ($request->filled('dari')) $query->whereDate('tgl_bayar', '>=', $request->dari);
        if ($request->filled('sampai')) $query->whereDate('tgl_bayar', '<=', $request->sampai);

        $pembayarans = $query->orderBy('tgl_bayar', 'desc')->paginate(20)->withQueryString();

        $totalHariIni = KeuanganPembayaran::whereDate('tgl_bayar', now())->sum('nominal');
        $totalBulanIni = KeuanganPembayaran::whereMonth('tgl_bayar', now()->month)
            ->whereYear('tgl_bayar', now()->year)->sum('nominal');

        return view('keuangan.pembayaran.index', compact('pembayarans', 'totalHariIni', 'totalBulanIni'));
    }

    /**
     * Form catat pembayaran (dari tagihan)
     */
    public function create(Request $request)
    {
        // Kalau ada tagihan_id, langsung dari tagihan
        $selectedTagihan = null;
        if ($request->filled('tagihan_id')) {
            $selectedTagihan = KeuanganTagihan::with(['keluarga', 'iuran'])->find($request->tagihan_id);
        }

        // Daftar tagihan belum lunas
        $tagihans = KeuanganTagihan::with(['keluarga', 'iuran'])
            ->whereIn('status', ['belum_bayar', 'sebagian', 'telat'])
            ->orderBy('jatuh_tempo')
            ->get();

        return view('keuangan.pembayaran.create', compact('tagihans', 'selectedTagihan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tagihan_id' => 'required|exists:keuangan_tagihan,id',
            'nominal' => 'required|numeric|min:1',
            'metode' => 'required|in:tunai,transfer,qris,ewallet',
            'tgl_bayar' => 'required|date',
            'no_referensi' => 'nullable|string|max:100',
            'bukti' => 'nullable|image|max:2048',
            'catatan' => 'nullable|string',
        ]);

        $tagihan = KeuanganTagihan::findOrFail($validated['tagihan_id']);

        if ($tagihan->status === 'lunas') {
            return back()->with('error', 'Tagihan sudah lunas.')->withInput();
        }

        // Upload bukti
        if ($request->hasFile('bukti')) {
            $validated['bukti'] = $request->file('bukti')->store('keuangan/bukti', 'public');
        }

        DB::beginTransaction();
        try {
            // Buat pembayaran
            $pembayaran = KeuanganPembayaran::create([
                'tagihan_id' => $tagihan->id,
                'keluarga_id' => $tagihan->keluarga_id,
                'nominal' => $validated['nominal'],
                'metode' => $validated['metode'],
                'tgl_bayar' => $validated['tgl_bayar'],
                'no_referensi' => $validated['no_referensi'] ?? null,
                'bukti' => $validated['bukti'] ?? null,
                'catatan' => $validated['catatan'] ?? null,
                'status' => 'verified',
                'diverifikasi_oleh' => auth()->id(),
                'dicatat_oleh' => auth()->id(),
            ]);

            // Update tagihan
            $totalBayar = $tagihan->total_dibayar + $validated['nominal'];
            $status = 'belum_bayar';

            if ($totalBayar >= $tagihan->nominal) {
                $status = 'lunas';
            } elseif ($totalBayar > 0) {
                $status = 'sebagian';
            }

            $tagihan->update([
                'total_dibayar' => $totalBayar,
                'tunggakan' => $tagihan->nominal - $totalBayar,
                'status' => $status,
                'tgl_bayar_lunas' => $status === 'lunas' ? $validated['tgl_bayar'] : null,
            ]);

            // Catat ke Kas (pemasukan)
            KeuanganKas::create([
                'jenis' => 'pemasukan',
                'kategori' => 'iuran',
                'nominal' => $validated['nominal'],
                'tgl_transaksi' => $validated['tgl_bayar'],
                'deskripsi' => 'Pembayaran ' . $tagihan->iuran->nama . ' - ' . $tagihan->keluarga->kepala_keluarga_nama,
                'pembayaran_id' => $pembayaran->id,
                'dicatat_oleh' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('keuangan.tagihan.show', $tagihan)->with('success', 'Pembayaran berhasil dicatat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function show(KeuanganPembayaran $pembayaran)
    {
        $pembayaran->load(['tagihan.iuran', 'keluarga', 'pencatat', 'verifikator']);
        return view('keuangan.pembayaran.show', compact('pembayaran'));
    }

    public function edit(KeuanganPembayaran $pembayaran)
    {
        return view('keuangan.pembayaran.edit', compact('pembayaran'));
    }

    public function update(Request $request, KeuanganPembayaran $pembayaran)
    {
        $validated = $request->validate([
            'nominal' => 'required|numeric|min:1',
            'metode' => 'required|in:tunai,transfer,qris,ewallet',
            'tgl_bayar' => 'required|date',
            'no_referensi' => 'nullable|string|max:100',
            'catatan' => 'nullable|string',
        ]);

        $pembayaran->update($validated);
        return redirect()->route('keuangan.pembayaran.show', $pembayaran)->with('success', 'Pembayaran diperbarui.');
    }

    public function destroy(KeuanganPembayaran $pembayaran)
    {
        if (!auth()->user()->hasAnyRole(['super_admin', 'bendahara'])) {
            return back()->with('error', 'Hanya bendahara/super admin yang bisa hapus.');
        }

        DB::beginTransaction();
        try {
            $tagihan = $pembayaran->tagihan;

            // Hapus kas terkait
            KeuanganKas::where('pembayaran_id', $pembayaran->id)->delete();

            $pembayaran->delete();

            // Recalculate tagihan
            if ($tagihan) {
                $total = $tagihan->pembayarans()->sum('nominal');
                $status = $total >= $tagihan->nominal ? 'lunas' : ($total > 0 ? 'sebagian' : 'belum_bayar');

                $tagihan->update([
                    'total_dibayar' => $total,
                    'tunggakan' => $tagihan->nominal - $total,
                    'status' => $status,
                ]);
            }

            DB::commit();
            return redirect()->route('keuangan.pembayaran.index')->with('success', 'Pembayaran dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}
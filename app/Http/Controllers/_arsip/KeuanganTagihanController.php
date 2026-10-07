<?php

namespace App\Http\Controllers;

use App\Models\KeuanganIuran;
use App\Models\KeuanganTagihan;
use App\Models\Keluarga;
use App\Models\Rt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KeuanganTagihanController extends Controller
{
    public function index(Request $request)
    {
        $query = KeuanganTagihan::with(['iuran', 'keluarga', 'rt']);
        $user = auth()->user();

        // Filter otomatis: Ketua RT hanya lihat RT-nya
        if ($user->hasRole('ketua_rt') && $user->rt_id && !$user->hasAnyRole(['super_admin', 'ketua_rw', 'bendahara', 'sekretaris'])) {
            $query->where('rt_id', $user->rt_id);
        }

        // Warga: hanya lihat tagihan keluarganya
        if ($user->hasRole('warga') && !$user->hasAnyRole(['super_admin', 'ketua_rw', 'ketua_rt', 'bendahara', 'sekretaris'])) {
            $query->where('keluarga_id', $user->keluarga_id ?? 0);
        }

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('periode')) $query->where('periode', $request->periode);
        if ($request->filled('iuran_id')) $query->where('iuran_id', $request->iuran_id);
        if ($request->filled('rt_id') && $user->hasAnyRole(['super_admin', 'ketua_rw', 'bendahara', 'sekretaris'])) {
            $query->where('rt_id', $request->rt_id);
        }
        if ($request->filled('q')) {
            $query->whereHas('keluarga', function ($sub) use ($request) {
                $sub->where('kepala_keluarga_nama', 'like', '%' . $request->q . '%')
                    ->orWhere('no_kk', 'like', '%' . $request->q . '%');
            });
        }

        $tagihans = $query->orderBy('jatuh_tempo', 'desc')->paginate(20)->withQueryString();
        $iurans = KeuanganIuran::where('is_active', true)->orderBy('nama')->get();
        $rts = Rt::orderBy('nomor_rt')->get();

        // Statistik
        $baseStats = KeuanganTagihan::query();
        if ($user->hasRole('ketua_rt') && $user->rt_id && !$user->hasAnyRole(['super_admin', 'ketua_rw', 'bendahara', 'sekretaris'])) {
            $baseStats->where('rt_id', $user->rt_id);
        }

        $stats = [
            'total' => (clone $baseStats)->count(),
            'belum_bayar' => (clone $baseStats)->where('status', 'belum_bayar')->count(),
            'lunas' => (clone $baseStats)->where('status', 'lunas')->count(),
            'tunggakan' => (clone $baseStats)->whereIn('status', ['belum_bayar', 'sebagian', 'telat'])->sum('tunggakan'),
        ];

        return view('keuangan.tagihan.index', compact('tagihans', 'iurans', 'rts', 'stats'));
    }

    /**
     * Form generate tagihan massal
     */
    public function create()
    {
        $iurans = KeuanganIuran::where('is_active', true)->orderBy('nama')->get();
        return view('keuangan.tagihan.create', compact('iurans'));
    }

    /**
     * Generate tagihan massal
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'iuran_id' => 'required|exists:keuangan_iuran,id',
            'periode' => 'required|string|max:20',
            'jatuh_tempo' => 'required|date',
        ]);

        $iuran = KeuanganIuran::findOrFail($validated['iuran_id']);

        // Ambil semua KK (filter by RT kalau iuran khusus RT)
        $query = Keluarga::where('status_keluarga', 'aktif');
        if ($iuran->rt_id) {
            $query->where('rt_id', $iuran->rt_id);
        }
        $keluargas = $query->get();

        if ($keluargas->isEmpty()) {
            return back()->with('error', 'Tidak ada KK aktif untuk iuran ini.');
        }

        DB::beginTransaction();
        try {
            $created = 0;
            $skipped = 0;

            foreach ($keluargas as $kk) {
                $exists = KeuanganTagihan::where('iuran_id', $iuran->id)
                    ->where('keluarga_id', $kk->id)
                    ->where('periode', $validated['periode'])
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                KeuanganTagihan::create([
                    'iuran_id' => $iuran->id,
                    'keluarga_id' => $kk->id,
                    'rt_id' => $kk->rt_id,
                    'periode' => $validated['periode'],
                    'nominal' => $iuran->nominal_default,
                    'total_dibayar' => 0,
                    'tunggakan' => $iuran->nominal_default,
                    'status' => 'belum_bayar',
                    'jatuh_tempo' => $validated['jatuh_tempo'],
                ]);

                $created++;
            }

            DB::commit();

            $msg = "Berhasil generate {$created} tagihan.";
            if ($skipped > 0) {
                $msg .= " {$skipped} dilewati (sudah ada).";
            }

            return redirect()->route('keuangan.tagihan.index')->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal generate: ' . $e->getMessage());
        }
    }

    public function show(KeuanganTagihan $tagihan)
    {
        $tagihan->load(['iuran', 'keluarga', 'rt', 'pembayarans.pencatat']);
        return view('keuangan.tagihan.show', compact('tagihan'));
    }

    public function edit(KeuanganTagihan $tagihan)
    {
        return view('keuangan.tagihan.edit', compact('tagihan'));
    }

    public function update(Request $request, KeuanganTagihan $tagihan)
    {
        $validated = $request->validate([
            'nominal' => 'required|numeric|min:0',
            'jatuh_tempo' => 'required|date',
            'status' => 'required|in:belum_bayar,sebagian,lunas,telat,batal',
            'keterangan' => 'nullable|string',
        ]);

        $validated['tunggakan'] = $validated['nominal'] - $tagihan->total_dibayar;

        $tagihan->update($validated);

        // ✅ FIX: keuangan.tagihan.show (bukan keuangantagihan.show)
        return redirect()->route('keuangan.tagihan.show', $tagihan)->with('success', 'Tagihan diperbarui.');
    }

    public function destroy(KeuanganTagihan $tagihan)
    {
        if ($tagihan->pembayarans()->count() > 0) {
            return back()->with('error', 'Tagihan sudah ada pembayaran. Tidak bisa dihapus.');
        }

        $tagihan->delete();
        return redirect()->route('keuangan.tagihan.index')->with('success', 'Tagihan dihapus.');
    }
}
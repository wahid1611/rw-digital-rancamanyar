<?php

namespace App\Http\Controllers;

use App\Models\KeuanganIuran;
use App\Models\Rt;
use Illuminate\Http\Request;

class KeuanganIuranController extends Controller
{
    public function index(Request $request)
    {
        $query = KeuanganIuran::with('rt')->withCount('tagihans');

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $iurans = $query->orderBy('kategori')->orderBy('nama')->paginate(15)->withQueryString();
        $rts = Rt::orderBy('nomor_rt')->get();

        return view('keuangan.iuran.index', compact('iurans', 'rts'));
    }

    public function create()
    {
        $rts = Rt::orderBy('nomor_rt')->get();
        return view('keuangan.iuran.create', compact('rts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kategori' => 'required|in:bulanan,keamanan,kebersihan,kesehatan,sosial,lainnya',
            'nominal_default' => 'required|numeric|min:0',
            'periode' => 'required|in:bulanan,triwulan,tahunan,sekali',
            'per_kk' => 'nullable|boolean',
            'rt_id' => 'nullable|exists:rt,id',
            'jatuh_tempo_tgl' => 'required|integer|min:1|max:28',
            'is_active' => 'nullable|boolean',
            'keterangan' => 'nullable|string',
        ]);

        $validated['per_kk'] = $request->boolean('per_kk', true);
        $validated['is_active'] = $request->boolean('is_active', true);

        KeuanganIuran::create($validated);

        return redirect()->route('keuangan.iuran.index')->with('success', 'Jenis iuran berhasil ditambahkan.');
    }

    public function show(KeuanganIuran $iuran)
    {
        $iuran->load(['rt'])->loadCount('tagihans');
        return view('keuangan.iuran.show', compact('iuran'));
    }

    public function edit(KeuanganIuran $iuran)
    {
        $rts = Rt::orderBy('nomor_rt')->get();
        return view('keuangan.iuran.edit', compact('iuran', 'rts'));
    }

    public function update(Request $request, KeuanganIuran $iuran)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kategori' => 'required|in:bulanan,keamanan,kebersihan,kesehatan,sosial,lainnya',
            'nominal_default' => 'required|numeric|min:0',
            'periode' => 'required|in:bulanan,triwulan,tahunan,sekali',
            'per_kk' => 'nullable|boolean',
            'rt_id' => 'nullable|exists:rt,id',
            'jatuh_tempo_tgl' => 'required|integer|min:1|max:28',
            'is_active' => 'nullable|boolean',
            'keterangan' => 'nullable|string',
        ]);

        $validated['per_kk'] = $request->boolean('per_kk', true);
        $validated['is_active'] = $request->boolean('is_active', true);

        $iuran->update($validated);

        return redirect()->route('keuangan.iuran.index')->with('success', 'Jenis iuran diperbarui.');
    }

    public function destroy(KeuanganIuran $iuran)
    {
        if ($iuran->tagihans()->count() > 0) {
            return back()->with('error', 'Jenis iuran masih punya tagihan. Hapus tagihan dulu.');
        }

        $iuran->delete();
        return redirect()->route('keuangan.iuran.index')->with('success', 'Jenis iuran dihapus.');
    }
}
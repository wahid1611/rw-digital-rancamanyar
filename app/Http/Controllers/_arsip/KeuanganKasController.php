<?php

namespace App\Http\Controllers;

use App\Models\KeuanganKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KeuanganKasController extends Controller
{
    public function index(Request $request)
    {
        $query = KeuanganKas::with('pencatat');

        if ($request->filled('jenis')) $query->where('jenis', $request->jenis);
        if ($request->filled('kategori')) $query->where('kategori', $request->kategori);
        if ($request->filled('dari')) $query->whereDate('tgl_transaksi', '>=', $request->dari);
        if ($request->filled('sampai')) $query->whereDate('tgl_transaksi', '<=', $request->sampai);

        $transaksis = $query->orderBy('tgl_transaksi', 'desc')->paginate(20)->withQueryString();

        // Statistik
        $masuk = (clone $query)->where('jenis', 'pemasukan')->sum('nominal');
        $keluar = (clone $query)->where('jenis', 'pengeluaran')->sum('nominal');

        $stats = [
            'masuk' => $masuk,
            'keluar' => $keluar,
            'saldo' => KeuanganKas::saldo(),
        ];

        return view('keuangan.kas.index', compact('transaksis', 'stats'));
    }

    public function create()
    {
        return view('keuangan.kas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis' => 'required|in:pemasukan,pengeluaran',
            'kategori' => 'required|string|max:50',
            'nominal' => 'required|numeric|min:1',
            'tgl_transaksi' => 'required|date',
            'deskripsi' => 'required|string',
            'bukti' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('bukti')) {
            $validated['bukti'] = $request->file('bukti')->store('keuangan/kas', 'public');
        }

        $validated['dicatat_oleh'] = auth()->id();

        KeuanganKas::create($validated);

        return redirect()->route('keuangan.kas.index')->with('success', 'Transaksi kas berhasil dicatat.');
    }

    public function show(KeuanganKas $kas)
    {
        $kas->load('pencatat', 'pembayaran');
        return view('keuangan.kas.show', ['transaksi' => $kas]);
    }

    public function edit(KeuanganKas $kas)
    {
        return view('keuangan.kas.edit', ['transaksi' => $kas]);
    }

    public function update(Request $request, KeuanganKas $kas)
    {
        $validated = $request->validate([
            'jenis' => 'required|in:pemasukan,pengeluaran',
            'kategori' => 'required|string|max:50',
            'nominal' => 'required|numeric|min:1',
            'tgl_transaksi' => 'required|date',
            'deskripsi' => 'required|string',
            'bukti' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('bukti')) {
            if ($kas->bukti && Storage::disk('public')->exists($kas->bukti)) {
                Storage::disk('public')->delete($kas->bukti);
            }
            $validated['bukti'] = $request->file('bukti')->store('keuangan/kas', 'public');
        }

        $kas->update($validated);

        return redirect()->route('keuangan.kas.index')->with('success', 'Transaksi diperbarui.');
    }

    public function destroy(KeuanganKas $kas)
    {
        // Kalau dari pembayaran iuran, jangan hapus dari sini
        if ($kas->pembayaran_id) {
            return back()->with('error', 'Transaksi ini dari pembayaran iuran. Hapus dari menu Pembayaran.');
        }

        if ($kas->bukti && Storage::disk('public')->exists($kas->bukti)) {
            Storage::disk('public')->delete($kas->bukti);
        }

        $kas->delete();
        return redirect()->route('keuangan.kas.index')->with('success', 'Transaksi dihapus.');
    }
}
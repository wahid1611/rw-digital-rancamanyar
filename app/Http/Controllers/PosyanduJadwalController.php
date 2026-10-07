<?php

namespace App\Http\Controllers;

use App\Models\PosyanduJadwal;
use App\Models\PosyanduPemeriksaan;
use Illuminate\Http\Request;

class PosyanduJadwalController extends Controller
{
    public function index(Request $request)
    {
        $query = PosyanduJadwal::with('petugas')->withCount('pemeriksaans');

        if ($request->filled('jenis')) $query->where('jenis', $request->jenis);
        if ($request->filled('status')) $query->where('status', $request->status);

        $jadwals = $query->orderBy('tanggal', 'desc')->paginate(15)->withQueryString();

        // Statistik
        $stats = [
            'total' => PosyanduJadwal::count(),
            'bulan_ini' => PosyanduJadwal::whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)->count(),
            'akan_datang' => PosyanduJadwal::where('tanggal', '>=', now())
                ->where('status', 'rencana')->count(),
        ];

        return view('posyandu.jadwal.index', compact('jadwals', 'stats'));
    }

    public function create()
    {
        return view('posyandu.jadwal.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'tanggal' => 'required|date',
            'jam_mulai' => 'nullable',
            'jam_selesai' => 'nullable',
            'lokasi' => 'nullable|string|max:150',
            'jenis' => 'required|in:balita,lansia,umum',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:rencana,berlangsung,selesai,batal',
        ]);

        $validated['petugas_id'] = auth()->id();

        PosyanduJadwal::create($validated);

        return redirect()->route('posyandu.jadwal.index')->with('success', 'Jadwal Posyandu dibuat.');
    }

    public function show(PosyanduJadwal $jadwal)
    {
        $jadwal->load(['petugas', 'pemeriksaans.warga', 'pemeriksaans.rt']);

        $stats = [
            'balita' => $jadwal->pemeriksaans->where('kategori', 'balita')->count(),
            'lansia' => $jadwal->pemeriksaans->where('kategori', 'lansia')->count(),
            'total' => $jadwal->pemeriksaans->count(),
        ];

        return view('posyandu.jadwal.show', compact('jadwal', 'stats'));
    }

    public function edit(PosyanduJadwal $jadwal)
    {
        return view('posyandu.jadwal.edit', compact('jadwal'));
    }

    public function update(Request $request, PosyanduJadwal $jadwal)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'tanggal' => 'required|date',
            'jam_mulai' => 'nullable',
            'jam_selesai' => 'nullable',
            'lokasi' => 'nullable|string|max:150',
            'jenis' => 'required|in:balita,lansia,umum',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:rencana,berlangsung,selesai,batal',
        ]);

        $jadwal->update($validated);

        return redirect()->route('posyandu.jadwal.show', $jadwal)->with('success', 'Jadwal diperbarui.');
    }

    public function destroy(PosyanduJadwal $jadwal)
    {
        $jadwal->delete();
        return redirect()->route('posyandu.jadwal.index')->with('success', 'Jadwal dihapus.');
    }
}
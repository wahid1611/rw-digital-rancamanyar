<?php

namespace App\Http\Controllers;

use App\Models\BansosProgram;
use App\Models\BansosPenerima;
use Illuminate\Http\Request;

class BansosProgramController extends Controller
{
    public function index(Request $request)
    {
        $query = BansosProgram::with(['pembuat'])->withCount(['penerimas', 'penyalurans']);

        if ($request->filled('kategori')) $query->where('kategori', $request->kategori);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('q')) $query->where('nama', 'like', '%' . $request->q . '%');

        $programs = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $stats = [
            'total' => BansosProgram::count(),
            'aktif' => BansosProgram::where('status', 'aktif')->count(),
            'penerima' => BansosPenerima::where('status_kelayakan', 'layak')->count(),
        ];

        return view('bansos.program.index', compact('programs', 'stats'));
    }

    public function create()
    {
        return view('bansos.program.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'kategori' => 'required|in:blt,pkh,bpnt,sembako,kesehatan,pendidikan,bencana,lainnya',
            'deskripsi' => 'nullable|string',
            'sumber' => 'nullable|string|max:150',
            'jenis_bantuan' => 'required|in:uang,barang,jasa,campuran',
            'nominal' => 'nullable|numeric|min:0',
            'satuan_bantuan' => 'nullable|string|max:50',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'periode' => 'nullable|string|max:50',
            'kriteria' => 'nullable|string',
            'status' => 'required|in:draft,aktif,selesai,batal',
        ]);

        $validated['dibuat_oleh'] = auth()->id();

        BansosProgram::create($validated);

        return redirect()->route('bansos.program.index')->with('success', 'Program Bansos berhasil dibuat.');
    }

    public function show(BansosProgram $program)
    {
        $program->load(['pembuat', 'penerimas.warga', 'penerimas.rt', 'penyalurans']);
        return view('bansos.program.show', compact('program'));
    }

    public function edit(BansosProgram $program)
    {
        return view('bansos.program.edit', compact('program'));
    }

    public function update(Request $request, BansosProgram $program)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'kategori' => 'required|in:blt,pkh,bpnt,sembako,kesehatan,pendidikan,bencana,lainnya',
            'deskripsi' => 'nullable|string',
            'sumber' => 'nullable|string|max:150',
            'jenis_bantuan' => 'required|in:uang,barang,jasa,campuran',
            'nominal' => 'nullable|numeric|min:0',
            'satuan_bantuan' => 'nullable|string|max:50',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'periode' => 'nullable|string|max:50',
            'kriteria' => 'nullable|string',
            'status' => 'required|in:draft,aktif,selesai,batal',
        ]);

        $program->update($validated);

        return redirect()->route('bansos.program.show', $program)->with('success', 'Program diperbarui.');
    }

    public function destroy(BansosProgram $program)
    {
        if ($program->penerimas()->count() > 0) {
            return back()->with('error', 'Program sudah punya penerima. Hapus penerima dulu.');
        }

        $program->delete();
        return redirect()->route('bansos.program.index')->with('success', 'Program dihapus.');
    }
}
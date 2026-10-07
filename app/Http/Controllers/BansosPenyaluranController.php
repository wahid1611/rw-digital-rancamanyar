<?php

namespace App\Http\Controllers;

use App\Models\BansosProgram;
use App\Models\BansosPenerima;
use App\Models\BansosPenyaluran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BansosPenyaluranController extends Controller
{
    public function index(Request $request)
    {
        $query = BansosPenyaluran::with(['penerima.warga', 'program', 'petugas']);

        if ($request->filled('program_id')) $query->where('program_id', $request->program_id);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('dari')) $query->whereDate('tanggal', '>=', $request->dari);
        if ($request->filled('sampai')) $query->whereDate('tanggal', '<=', $request->sampai);

        $penyalurans = $query->orderBy('tanggal', 'desc')->paginate(20)->withQueryString();
        $programs = BansosProgram::orderBy('nama')->get();

        $stats = [
            'total_transaksi' => BansosPenyaluran::where('status', 'disalurkan')->count(),
            'total_nominal' => BansosPenyaluran::where('status', 'disalurkan')->sum('nominal'),
        ];

        return view('bansos.penyaluran.index', compact('penyalurans', 'programs', 'stats'));
    }

    public function create(Request $request)
    {
        $programs = BansosProgram::where('status', 'aktif')->orderBy('nama')->get();

        $penerimas = BansosPenerima::with(['warga', 'rt'])
            ->where('status_kelayakan', 'layak')
            ->when($request->filled('program_id'), fn($q) => $q->where('program_id', $request->program_id))
            ->orderBy('nama_penerima')
            ->get();

        $selectedPenerima = $request->filled('penerima_id') 
            ? BansosPenerima::find($request->penerima_id) : null;

        return view('bansos.penyaluran.create', compact('programs', 'penerimas', 'selectedPenerima'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'penerima_id' => 'required|exists:bansos_penerima,id',
            'tanggal' => 'required|date',
            'periode' => 'nullable|string|max:50',
            'nominal' => 'nullable|numeric|min:0',
            'jenis_bantuan' => 'nullable|string|max:50',
            'deskripsi_barang' => 'nullable|string',
            'foto_bukti' => 'nullable|image|max:2048',
            'status' => 'required|in:dijadwalkan,disalurkan,dibatalkan',
            'catatan' => 'nullable|string',
        ]);

        $penerima = BansosPenerima::findOrFail($validated['penerima_id']);

        if ($penerima->status_kelayakan !== 'layak') {
            return back()->with('error', 'Penerima belum diverifikasi layak.')->withInput();
        }

        if ($request->hasFile('foto_bukti')) {
            $validated['foto_bukti'] = $request->file('foto_bukti')->store('bansos/bukti', 'public');
        }

        $validated['program_id'] = $penerima->program_id;
        $validated['petugas_id'] = auth()->id();

        BansosPenyaluran::create($validated);

        return redirect()->route('bansos.penyaluran.index')
            ->with('success', 'Penyaluran berhasil dicatat.');
    }

    public function show(BansosPenyaluran $penyaluran)
    {
        $penyaluran->load(['penerima.warga', 'program', 'petugas']);
        return view('bansos.penyaluran.show', compact('penyaluran'));
    }

    public function edit(BansosPenyaluran $penyaluran)
    {
        return view('bansos.penyaluran.edit', compact('penyaluran'));
    }

    public function update(Request $request, BansosPenyaluran $penyaluran)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'periode' => 'nullable|string|max:50',
            'nominal' => 'nullable|numeric|min:0',
            'deskripsi_barang' => 'nullable|string',
            'foto_bukti' => 'nullable|image|max:2048',
            'status' => 'required|in:dijadwalkan,disalurkan,dibatalkan',
            'catatan' => 'nullable|string',
        ]);

        if ($request->hasFile('foto_bukti')) {
            if ($penyaluran->foto_bukti) Storage::disk('public')->delete($penyaluran->foto_bukti);
            $validated['foto_bukti'] = $request->file('foto_bukti')->store('bansos/bukti', 'public');
        }

        $penyaluran->update($validated);

        return redirect()->route('bansos.penyaluran.show', $penyaluran)->with('success', 'Penyaluran diperbarui.');
    }

    public function destroy(BansosPenyaluran $penyaluran)
    {
        if ($penyaluran->foto_bukti) Storage::disk('public')->delete($penyaluran->foto_bukti);
        $penyaluran->delete();
        return redirect()->route('bansos.penyaluran.index')->with('success', 'Penyaluran dihapus.');
    }
}
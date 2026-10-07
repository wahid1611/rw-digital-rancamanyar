<?php

namespace App\Http\Controllers;

use App\Models\BansosProgram;
use App\Models\BansosPenerima;
use App\Models\Warga;
use App\Models\Rt;
use Illuminate\Http\Request;

class BansosPenerimaController extends Controller
{
    public function index(Request $request)
    {
        $query = BansosPenerima::with(['program', 'warga', 'rt', 'verifikator']);
        $user = auth()->user();

        // Ketua RT hanya lihat RT-nya
        if ($user->hasRole('ketua_rt') && $user->rt_id && !$user->hasAnyRole(['super_admin', 'ketua_rw', 'sekretaris'])) {
            $query->where('rt_id', $user->rt_id);
        }

        if ($request->filled('program_id')) $query->where('program_id', $request->program_id);
        if ($request->filled('status_kelayakan')) $query->where('status_kelayakan', $request->status_kelayakan);
        if ($request->filled('rt_id') && $user->hasAnyRole(['super_admin', 'ketua_rw', 'sekretaris'])) {
            $query->where('rt_id', $request->rt_id);
        }
        if ($request->filled('q')) {
            $query->where('nama_penerima', 'like', '%' . $request->q . '%');
        }

        $penerimas = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $programs = BansosProgram::orderBy('nama')->get();
        $rts = Rt::orderBy('nomor_rt')->get();

        $stats = [
            'total' => BansosPenerima::count(),
            'pending' => BansosPenerima::where('status_kelayakan', 'pending')->count(),
            'layak' => BansosPenerima::where('status_kelayakan', 'layak')->count(),
        ];

        return view('bansos.penerima.index', compact('penerimas', 'programs', 'rts', 'stats'));
    }

    public function create(Request $request)
    {
        $programs = BansosProgram::whereIn('status', ['aktif', 'draft'])->orderBy('nama')->get();
        $rts = Rt::orderBy('nomor_rt')->get();

        $selectedProgram = $request->filled('program_id') 
            ? BansosProgram::find($request->program_id) : null;

        return view('bansos.penerima.create', compact('programs', 'rts', 'selectedProgram'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:bansos_program,id',
            'warga_id' => 'required|exists:warga,id',
            'alasan_layak' => 'nullable|string',
            'skor_kelayakan' => 'nullable|integer|min:0|max:100',
            'no_hp' => 'nullable|string|max:20',
        ]);

        // Cek sudah terdaftar?
        $exists = BansosPenerima::where('program_id', $validated['program_id'])
            ->where('warga_id', $validated['warga_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Warga ini sudah terdaftar di program ini.')->withInput();
        }

        $warga = Warga::find($validated['warga_id']);

        $validated['keluarga_id'] = $warga->keluarga_id;
        $validated['rt_id'] = $warga->rt_id;
        $validated['nik_penerima'] = $warga->nik;
        $validated['nama_penerima'] = $warga->nama;
        $validated['status_kelayakan'] = 'pending';
        $validated['skor_kelayakan'] = $validated['skor_kelayakan'] ?? 0;

        BansosPenerima::create($validated);

        return redirect()->route('bansos.penerima.index')
            ->with('success', 'Penerima berhasil didaftarkan.');
    }

    public function show(BansosPenerima $penerima)
    {
        $penerima->load(['program', 'warga', 'rt', 'verifikator', 'penyalurans.petugas']);
        return view('bansos.penerima.show', compact('penerima'));
    }

    public function edit(BansosPenerima $penerima)
    {
        $programs = BansosProgram::orderBy('nama')->get();
        $wargas = Warga::with('rt')->orderBy('nama')->get();
        return view('bansos.penerima.edit', compact('penerima', 'programs', 'wargas'));
    }

    public function update(Request $request, BansosPenerima $penerima)
    {
        $validated = $request->validate([
            'alasan_layak' => 'nullable|string',
            'skor_kelayakan' => 'nullable|integer|min:0|max:100',
            'no_hp' => 'nullable|string|max:20',
        ]);

        $penerima->update($validated);

        return redirect()->route('bansos.penerima.show', $penerima)->with('success', 'Penerima diperbarui.');
    }

    public function destroy(BansosPenerima $penerima)
    {
        if ($penerima->penyalurans()->count() > 0) {
            return back()->with('error', 'Penerima sudah ada penyaluran. Hapus penyaluran dulu.');
        }

        $penerima->delete();
        return redirect()->route('bansos.penerima.index')->with('success', 'Penerima dihapus.');
    }

    /**
     * Verifikasi kelayakan
     */
    public function verifikasi(Request $request, BansosPenerima $penerima)
    {
        $request->validate([
            'status_kelayakan' => 'required|in:layak,tidak_layak',
            'catatan_verifikasi' => 'nullable|string',
        ]);

        $penerima->update([
            'status_kelayakan' => $request->status_kelayakan,
            'verifikator_id' => auth()->id(),
            'verified_at' => now(),
            'catatan_verifikasi' => $request->catatan_verifikasi,
        ]);

        return back()->with('success', 'Verifikasi kelayakan berhasil.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use App\Models\UmkmProduk;
use App\Models\Rt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UmkmController extends Controller
{
    public function index(Request $request)
    {
        $query = Umkm::with(['user', 'rt'])->withCount('produks');
        $user = auth()->user();

        // Warga biasa: hanya lihat UMKM aktif
        if (!$user->hasAnyRole(['super_admin', 'ketua_rw', 'ketua_rt', 'sekretaris'])) {
            $query->aktif();
        }

        // Filter manual
        if ($request->filled('kategori')) $query->where('kategori', $request->kategori);
        if ($request->filled('rt_id')) $query->where('rt_id', $request->rt_id);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('q')) {
            $query->where(function ($sub) use ($request) {
                $sub->where('nama_usaha', 'like', '%' . $request->q . '%')
                    ->orWhere('deskripsi', 'like', '%' . $request->q . '%');
            });
        }

        $umkms = $query->orderBy('views', 'desc')->paginate(12)->withQueryString();
        $rts = Rt::orderBy('nomor_rt')->get();

        // Statistik
        $stats = [
            'total' => Umkm::count(),
            'aktif' => Umkm::where('status', 'aktif')->count(),
            'pending' => Umkm::where('status', 'pending')->count(),
        ];

        return view('umkm.index', compact('umkms', 'rts', 'stats'));
    }

    public function create()
    {
        $rts = Rt::orderBy('nomor_rt')->get();
        return view('umkm.create', compact('rts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_usaha' => 'required|string|max:150',
            'kategori' => 'required|in:makanan,minuman,jasa,fashion,kerajinan,pertanian,elektronik,lainnya',
            'deskripsi' => 'nullable|string',
            'no_hp' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'instagram' => 'nullable|string|max:100',
            'alamat' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'jam_operasional' => 'nullable|string|max:100',
            'logo' => 'nullable|image|max:2048',
            'foto_usaha' => 'nullable|image|max:2048',
            'rt_id' => 'nullable|exists:rt,id',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('umkm/logo', 'public');
        }
        if ($request->hasFile('foto_usaha')) {
            $validated['foto_usaha'] = $request->file('foto_usaha')->store('umkm/foto', 'public');
        }

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';

        $umkm = Umkm::create($validated);

        return redirect()->route('umkm.show', $umkm)
            ->with('success', 'UMKM berhasil didaftarkan. Menunggu verifikasi.');
    }

    public function show(Umkm $umkm)
    {
        $umkm->load(['user', 'rt', 'produks', 'verifikator']);
        $umkm->increment('views');
        return view('umkm.show', compact('umkm'));
    }

    public function edit(Umkm $umkm)
    {
        $rts = Rt::orderBy('nomor_rt')->get();
        return view('umkm.edit', compact('umkm', 'rts'));
    }

    public function update(Request $request, Umkm $umkm)
    {
        $validated = $request->validate([
            'nama_usaha' => 'required|string|max:150',
            'kategori' => 'required|in:makanan,minuman,jasa,fashion,kerajinan,pertanian,elektronik,lainnya',
            'deskripsi' => 'nullable|string',
            'no_hp' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'instagram' => 'nullable|string|max:100',
            'alamat' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'jam_operasional' => 'nullable|string|max:100',
            'logo' => 'nullable|image|max:2048',
            'foto_usaha' => 'nullable|image|max:2048',
            'rt_id' => 'nullable|exists:rt,id',
        ]);

        if ($request->hasFile('logo')) {
            if ($umkm->logo) Storage::disk('public')->delete($umkm->logo);
            $validated['logo'] = $request->file('logo')->store('umkm/logo', 'public');
        }
        if ($request->hasFile('foto_usaha')) {
            if ($umkm->foto_usaha) Storage::disk('public')->delete($umkm->foto_usaha);
            $validated['foto_usaha'] = $request->file('foto_usaha')->store('umkm/foto', 'public');
        }

        $umkm->update($validated);

        return redirect()->route('umkm.show', $umkm)->with('success', 'UMKM diperbarui.');
    }

    public function destroy(Umkm $umkm)
    {
        if ($umkm->logo) Storage::disk('public')->delete($umkm->logo);
        if ($umkm->foto_usaha) Storage::disk('public')->delete($umkm->foto_usaha);

        foreach ($umkm->produks as $p) {
            if ($p->foto) Storage::disk('public')->delete($p->foto);
        }

        $umkm->delete();
        return redirect()->route('umkm.index')->with('success', 'UMKM dihapus.');
    }

    /**
     * Verifikasi UMKM
     */
    public function verifikasi(Request $request, Umkm $umkm)
    {
        $request->validate([
            'status' => 'required|in:aktif,nonaktif,ditolak',
            'catatan_verifikasi' => 'nullable|string',
        ]);

        $umkm->update([
            'status' => $request->status,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'catatan_verifikasi' => $request->catatan_verifikasi,
        ]);

        return back()->with('success', 'UMKM berhasil diverifikasi.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Tamu;
use App\Models\User;
use App\Models\Rt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TamuController extends Controller
{
    public function index(Request $request)
    {
        $query = Tamu::with(['tujuanUser', 'tujuanRt', 'petugas']);
        $user = auth()->user();

        // Filter otomatis: Ketua RT hanya lihat tamu ke RT-nya
        if ($user->hasRole('ketua_rt') && $user->rt_id && !$user->hasAnyRole(['super_admin', 'ketua_rw', 'security'])) {
            $query->where(function ($q) use ($user) {
                $q->where('tujuan_rt_id', $user->rt_id)
                  ->orWhereHas('tujuanUser', fn($sub) => $sub->where('rt_id', $user->rt_id));
            });
        }

        // Warga: hanya lihat tamu yang datang ke mereka
        if ($user->hasRole('warga') && !$user->hasAnyRole(['super_admin', 'ketua_rw', 'ketua_rt', 'security', 'sekretaris'])) {
            $query->where('tujuan_user_id', $user->id);
        }

        // Filter manual
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('tujuan_tipe')) $query->where('tujuan_tipe', $request->tujuan_tipe);
        if ($request->filled('dari')) $query->whereDate('waktu_masuk', '>=', $request->dari);
        if ($request->filled('sampai')) $query->whereDate('waktu_masuk', '<=', $request->sampai);
        if ($request->filled('q')) {
            $query->where(function ($sub) use ($request) {
                $sub->where('nama', 'like', '%' . $request->q . '%')
                    ->orWhere('kode_tamu', 'like', '%' . $request->q . '%')
                    ->orWhere('no_hp', 'like', '%' . $request->q . '%');
            });
        }

        $tamus = $query->orderBy('waktu_masuk', 'desc')->paginate(20)->withQueryString();

        // Statistik
        $stats = [
            'total' => Tamu::count(),
            'sedang_di_dalam' => Tamu::where('status', 'masuk')->count(),
            'hari_ini' => Tamu::whereDate('waktu_masuk', now())->count(),
        ];

        return view('tamu.index', compact('tamus', 'stats'));
    }

    public function create()
    {
        $rts = Rt::orderBy('nomor_rt')->get();
        $wargas = User::whereNotNull('rt_id')
            ->whereIn('status_akun', ['aktif'])
            ->orderBy('name')
            ->get();
        return view('tamu.create', compact('rts', 'wargas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'no_hp' => 'nullable|string|max:20',
            'no_identitas' => 'nullable|string|max:50',
            'jenis_identitas' => 'nullable|in:KTP,SIM,Paspor,Lainnya',
            'alamat_asal' => 'nullable|string|max:200',
            'instansi' => 'nullable|string|max:150',
            'tujuan_tipe' => 'required|in:warga,rw,rt,umum,lainnya',
            'tujuan_user_id' => 'nullable|exists:users,id',
            'tujuan_rt_id' => 'nullable|exists:rt,id',
            'keperluan' => 'required|string',
            'jenis_kendaraan' => 'nullable|string|max:50',
            'plat_nomor' => 'nullable|string|max:20',
            'foto_tamu' => 'nullable|image|max:2048',
            'foto_ktp' => 'nullable|image|max:2048',
            'catatan' => 'nullable|string',
        ]);

        if ($request->hasFile('foto_tamu')) {
            $validated['foto_tamu'] = $request->file('foto_tamu')->store('tamu/foto', 'public');
        }
        if ($request->hasFile('foto_ktp')) {
            $validated['foto_ktp'] = $request->file('foto_ktp')->store('tamu/ktp', 'public');
        }

        $validated['waktu_masuk'] = now();
        $validated['status'] = 'masuk';
        $validated['petugas_id'] = auth()->id();

        $tamu = Tamu::create($validated);

        return redirect()->route('tamu.show', $tamu)
            ->with('success', 'Tamu berhasil dicatat. Kode: ' . $tamu->kode_tamu);
    }

    public function show(Tamu $tamu)
    {
        $tamu->load(['tujuanUser', 'tujuanRt', 'petugas']);
        return view('tamu.show', compact('tamu'));
    }

    public function edit(Tamu $tamu)
    {
        $rts = Rt::orderBy('nomor_rt')->get();
        $wargas = User::whereNotNull('rt_id')->orderBy('name')->get();
        return view('tamu.edit', compact('tamu', 'rts', 'wargas'));
    }

    public function update(Request $request, Tamu $tamu)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'no_hp' => 'nullable|string|max:20',
            'no_identitas' => 'nullable|string|max:50',
            'jenis_identitas' => 'nullable|in:KTP,SIM,Paspor,Lainnya',
            'alamat_asal' => 'nullable|string|max:200',
            'instansi' => 'nullable|string|max:150',
            'tujuan_tipe' => 'required|in:warga,rw,rt,umum,lainnya',
            'tujuan_user_id' => 'nullable|exists:users,id',
            'tujuan_rt_id' => 'nullable|exists:rt,id',
            'keperluan' => 'required|string',
            'jenis_kendaraan' => 'nullable|string|max:50',
            'plat_nomor' => 'nullable|string|max:20',
            'foto_tamu' => 'nullable|image|max:2048',
            'foto_ktp' => 'nullable|image|max:2048',
            'catatan' => 'nullable|string',
        ]);

        if ($request->hasFile('foto_tamu')) {
            if ($tamu->foto_tamu) Storage::disk('public')->delete($tamu->foto_tamu);
            $validated['foto_tamu'] = $request->file('foto_tamu')->store('tamu/foto', 'public');
        }
        if ($request->hasFile('foto_ktp')) {
            if ($tamu->foto_ktp) Storage::disk('public')->delete($tamu->foto_ktp);
            $validated['foto_ktp'] = $request->file('foto_ktp')->store('tamu/ktp', 'public');
        }

        $tamu->update($validated);

        return redirect()->route('tamu.show', $tamu)->with('success', 'Data tamu diperbarui.');
    }

    public function destroy(Tamu $tamu)
    {
        if ($tamu->foto_tamu) Storage::disk('public')->delete($tamu->foto_tamu);
        if ($tamu->foto_ktp) Storage::disk('public')->delete($tamu->foto_ktp);

        $tamu->delete();
        return redirect()->route('tamu.index')->with('success', 'Data tamu dihapus.');
    }

    /**
     * Check-out tamu
     */
 public function checkout(Request $request, Tamu $tamu)
{
    if ($tamu->status !== 'masuk') {
        return back()->with('error', 'Tamu sudah keluar.');
    }

    $request->validate([
        'catatan_keluar' => 'required|string|max:500',
    ]);

    $tamu->update([
        'waktu_keluar' => now(),
        'status' => 'keluar',
        'catatan_keluar' => $request->catatan_keluar,
    ]);

    $tamu->refresh();

    return back()->with('success', 'Tamu berhasil check-out. Durasi: ' . $tamu->durasi_kunjungan);
}

    
}
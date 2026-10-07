<?php

namespace App\Http\Controllers;

use App\Models\BansosProgram;
use App\Models\BansosPenerima;
use App\Models\BansosPenyaluran;
use Illuminate\Http\Request;

class BansosController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'program'); // default: program

        // === STATISTIK GLOBAL ===
        $stats = [
            'total_program'    => BansosProgram::count(),
            'program_aktif'    => BansosProgram::where('status', 'aktif')->count(),
            'penerima_layak'   => BansosPenerima::where('status_kelayakan', 'layak')->count(),
            'total_disalurkan' => BansosPenyaluran::where('status', 'disalurkan')->sum('nominal'),
        ];

        // === DATA PER TAB ===
        $programs = $penerimas = $penyalurans = null;

        if ($tab === 'program') {
            $query = BansosProgram::with('pembuat')->withCount(['penerimas', 'penyalurans']);
            if ($request->filled('q')) $query->where('nama', 'like', '%' . $request->q . '%');
            if ($request->filled('kategori')) $query->where('kategori', $request->kategori);
            if ($request->filled('status')) $query->where('status', $request->status);
            $programs = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        }

        if ($tab === 'penerima') {
            $query = BansosPenerima::with(['program', 'warga', 'rt', 'verifikator']);
            $user = auth()->user();

            if ($user->hasRole('ketua_rt') && $user->rt_id && !$user->hasAnyRole(['super_admin', 'ketua_rw', 'sekretaris'])) {
                $query->where('rt_id', $user->rt_id);
            }
            if ($request->filled('program_id')) $query->where('program_id', $request->program_id);
            if ($request->filled('status_kelayakan')) $query->where('status_kelayakan', $request->status_kelayakan);
            if ($request->filled('q')) $query->where('nama_penerima', 'like', '%' . $request->q . '%');
            $penerimas = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        }

        if ($tab === 'penyaluran') {
            $query = BansosPenyaluran::with(['penerima.warga', 'program', 'petugas']);
            if ($request->filled('program_id')) $query->where('program_id', $request->program_id);
            if ($request->filled('status')) $query->where('status', $request->status);
            $penyalurans = $query->orderBy('tanggal', 'desc')->paginate(15)->withQueryString();
        }

        $programList = BansosProgram::orderBy('nama')->get();

        return view('bansos.index', compact(
            'tab',
            'stats',
            'programs',
            'penerimas',
            'penyalurans',
            'programList'
        ));
    }
}
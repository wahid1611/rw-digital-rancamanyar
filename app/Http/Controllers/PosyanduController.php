<?php

namespace App\Http\Controllers;

use App\Models\PosyanduJadwal;
use Illuminate\Http\Request;

class PosyanduController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'jadwal');

        // Statistik Global
        $stats = [
            'total_jadwal' => PosyanduJadwal::count(),
        ];

        // Data per tab
        $jadwals = null;

        if ($tab === 'jadwal') {
            $query = PosyanduJadwal::query();

            if ($request->filled('q')) {
                $query->where('nama', 'like', '%' . $request->q . '%');
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $jadwals = $query->orderBy('tanggal', 'desc')->paginate(15)->withQueryString();
        }

        return view('posyandu.index', compact('tab', 'stats', 'jadwals'));
    }
}
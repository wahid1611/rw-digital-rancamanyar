<?php

namespace App\Http\Controllers;

use App\Models\Rw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanPembayaranController extends Controller
{
    /**
     * Halaman pengaturan pembayaran (QRIS + Bank)
     */
    public function index()
    {
        $rw = Rw::first();

        if (!$rw) {
            return redirect()->route('dashboard')
                ->with('error', 'Data RW belum ada. Hubungi Super Admin.');
        }

        return view('keuangan.setting-pembayaran', compact('rw'));
    }

    /**
     * Simpan pengaturan pembayaran
     */
    public function update(Request $request)
    {
        $rw = Rw::first();

        if (!$rw) {
            return back()->with('error', 'Data RW belum ada.');
        }

        $validated = $request->validate([
            'bank_nama' => 'nullable|string|max:100',
            'bank_rekening' => 'nullable|string|max:50',
            'bank_atas_nama' => 'nullable|string|max:100',
            'kontak_bendahara' => 'nullable|string|max:20',
            'foto_qris' => 'nullable|image|max:2048',
        ]);

        // Handle upload foto QRIS
        if ($request->hasFile('foto_qris')) {
            // Hapus foto lama
            if ($rw->foto_qris && Storage::disk('public')->exists($rw->foto_qris)) {
                Storage::disk('public')->delete($rw->foto_qris);
            }

            $validated['foto_qris'] = $request->file('foto_qris')->store('rw/qris', 'public');
        }

        $rw->update($validated);

        return redirect()->route('pengaturan.pembayaran.index')
            ->with('success', 'Pengaturan pembayaran berhasil disimpan.');
    }

    /**
     * Hapus foto QRIS
     */
    public function hapusQris()
    {
        $rw = Rw::first();

        if ($rw && $rw->foto_qris && Storage::disk('public')->exists($rw->foto_qris)) {
            Storage::disk('public')->delete($rw->foto_qris);
            $rw->update(['foto_qris' => null]);
        }

        return back()->with('success', 'Foto QRIS berhasil dihapus.');
    }
}
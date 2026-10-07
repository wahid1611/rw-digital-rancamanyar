<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use App\Models\UmkmProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UmkmProdukController extends Controller
{
    public function index(Request $request)
    {
        $query = UmkmProduk::with('umkm');

        if ($request->filled('umkm_id')) {
            $query->where('umkm_id', $request->umkm_id);
        }

        $produks = $query->orderBy('urutan')->paginate(20)->withQueryString();
        return view('umkm.produk.index', compact('produks'));
    }

    public function create(Request $request)
    {
        $umkm = Umkm::findOrFail($request->umkm_id);
        return view('umkm.produk.create', compact('umkm'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'umkm_id' => 'required|exists:umkm,id',
            'nama' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:30',
            'foto' => 'nullable|image|max:2048',
            'is_tersedia' => 'nullable|boolean',
            'is_unggulan' => 'nullable|boolean',
            'urutan' => 'nullable|integer',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('umkm/produk', 'public');
        }

        $validated['is_tersedia'] = $request->boolean('is_tersedia', true);
        $validated['is_unggulan'] = $request->boolean('is_unggulan');
        $validated['urutan'] = $validated['urutan'] ?? 0;

        UmkmProduk::create($validated);

        return redirect()->route('umkm.show', $validated['umkm_id'])
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(UmkmProduk $produk)
    {
        $produk->load('umkm');
        return view('umkm.produk.show', compact('produk'));
    }

    public function edit(UmkmProduk $produk)
    {
        return view('umkm.produk.edit', compact('produk'));
    }

    public function update(Request $request, UmkmProduk $produk)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:30',
            'foto' => 'nullable|image|max:2048',
            'is_tersedia' => 'nullable|boolean',
            'is_unggulan' => 'nullable|boolean',
            'urutan' => 'nullable|integer',
        ]);

        if ($request->hasFile('foto')) {
            if ($produk->foto) Storage::disk('public')->delete($produk->foto);
            $validated['foto'] = $request->file('foto')->store('umkm/produk', 'public');
        }

        $validated['is_tersedia'] = $request->boolean('is_tersedia', true);
        $validated['is_unggulan'] = $request->boolean('is_unggulan');
        $validated['urutan'] = $validated['urutan'] ?? 0;

        $produk->update($validated);

        return redirect()->route('umkm.show', $produk->umkm_id)->with('success', 'Produk diperbarui.');
    }

    public function destroy(UmkmProduk $produk)
    {
        if ($produk->foto) Storage::disk('public')->delete($produk->foto);
        $umkmId = $produk->umkm_id;
        $produk->delete();
        return redirect()->route('umkm.show', $umkmId)->with('success', 'Produk dihapus.');
    }
}
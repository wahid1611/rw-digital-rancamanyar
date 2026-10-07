<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UmkmProduk extends Model
{
    protected $table = 'umkm_produk';

    protected $fillable = [
        'umkm_id', 'nama', 'deskripsi', 'harga', 'satuan',
        'foto', 'is_tersedia', 'is_unggulan', 'urutan',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
            'is_tersedia' => 'boolean',
            'is_unggulan' => 'boolean',
        ];
    }

    public function umkm() { return $this->belongsTo(Umkm::class); }

    public function getFotoUrlAttribute()
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }
}
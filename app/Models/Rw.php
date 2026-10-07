<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rw extends Model
{
    protected $table = 'rw';

    protected $fillable = [
        'kode_rw',
        'nama_rw',
        'desa',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'ketua_rw_nama',
        'alamat_sekretariat',
        'foto_qris',
        'bank_nama',
        'bank_rekening', 
        'bank_atas_nama', 
        'kontak_bendahara',
    ];

    public function rts()
    {
        return $this->hasMany(Rt::class);
    }

    public function getFotoQrisUrlAttribute()
    {
        return $this->foto_qris ? asset('storage/' . $this->foto_qris) : null;
    }
}
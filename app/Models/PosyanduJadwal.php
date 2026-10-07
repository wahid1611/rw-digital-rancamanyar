<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosyanduJadwal extends Model
{
    protected $table = 'posyandu_jadwal';

    protected $fillable = [
        'nama', 'tanggal', 'jam_mulai', 'jam_selesai',
        'lokasi', 'jenis', 'keterangan', 'status', 'petugas_id',
    ];

    protected function casts(): array
    {
        return ['tanggal' => 'date'];
    }

    public function petugas() { return $this->belongsTo(User::class, 'petugas_id'); }
    public function pemeriksaans() { return $this->hasMany(PosyanduPemeriksaan::class, 'jadwal_id'); }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'rencana' => 'bg-secondary',
            'berlangsung' => 'bg-warning text-dark',
            'selesai' => 'bg-success',
            'batal' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getJenisLabelAttribute()
    {
        return match ($this->jenis) {
            'balita' => 'Balita',
            'lansia' => 'Lansia',
            default => 'Umum',
        };
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BansosPenerima extends Model
{
    protected $table = 'bansos_penerima';

    protected $fillable = [
        'program_id', 'warga_id', 'keluarga_id', 'rt_id',
        'nik_penerima', 'nama_penerima', 'no_hp',
        'alasan_layak', 'status_kelayakan', 'skor_kelayakan',
        'verifikator_id', 'verified_at', 'catatan_verifikasi',
    ];

    protected function casts(): array
    {
        return ['verified_at' => 'datetime'];
    }

    public function program() { return $this->belongsTo(BansosProgram::class, 'program_id'); }
    public function warga() { return $this->belongsTo(Warga::class); }
    public function keluarga() { return $this->belongsTo(Keluarga::class); }
    public function rt() { return $this->belongsTo(Rt::class); }
    public function verifikator() { return $this->belongsTo(User::class, 'verifikator_id'); }
    public function penyalurans() { return $this->hasMany(BansosPenyaluran::class, 'penerima_id'); }

    // Accessor
    public function getStatusKelayakanBadgeAttribute()
    {
        return match ($this->status_kelayakan) {
            'pending' => 'bg-warning text-dark',
            'layak' => 'bg-success',
            'tidak_layak' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getStatusKelayakanLabelAttribute()
    {
        return match ($this->status_kelayakan) {
            'pending' => 'Menunggu Verifikasi',
            'layak' => 'Layak',
            'tidak_layak' => 'Tidak Layak',
            default => $this->status_kelayakan,
        };
    }

    public function getTotalDiterimaAttribute()
    {
        return $this->penyalurans()->where('status', 'disalurkan')->sum('nominal');
    }
}
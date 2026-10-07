<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeuanganIuran extends Model
{
    protected $table = 'keuangan_iuran';

    protected $fillable = [
        'nama', 'pemilik', 'pemilik_rt_id', 'kategori', 'nominal_default',
        'periode', 'per_kk', 'rt_id', 'jatuh_tempo_tgl', 'is_active', 'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'nominal_default' => 'decimal:2',
            'per_kk' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // ============ RELASI ============
    public function rt() { return $this->belongsTo(Rt::class); }
    public function rtPemilik() { return $this->belongsTo(Rt::class, 'pemilik_rt_id');  }
    public function tagihans() { return $this->hasMany(KeuanganTagihan::class, 'iuran_id'); }

    // ============ SCOPE (PENTING) ============
    /**
     * Filter iuran milik RW
     */
    public function scopePemilikRw($query)
{
    return $query->where('pemilik', 'rw');
}

public function scopePemilikRt($query, $rtId)
{
    return $query->where('pemilik', 'rt')->where('pemilik_rt_id', $rtId);
}

public function scopeUntukUser($query, $user)
{
    if ($user->hasRole('bendahara')) {
        return $query->pemilikRw();
    }
    if ($user->hasRole('bendahara_rt') && $user->rt_id) {
        return $query->pemilikRt($user->rt_id);
    }
    return $query;
}
    // ============ ACCESSOR ============
    public function getPeriodeLabelAttribute()
    {
        return match ($this->periode) {
            'bulanan' => 'Bulanan',
            'triwulan' => 'Triwulan',
            'tahunan' => 'Tahunan',
            'sekali' => 'Sekali',
            default => $this->periode,
        };
    }

    public function getPemilikLabelAttribute()
    {
        if ($this->pemilik === 'rw') {
            return 'RW 07';
        }
        return $this->rtPemilik->nama_rt ?? 'RT ?';
    }
}
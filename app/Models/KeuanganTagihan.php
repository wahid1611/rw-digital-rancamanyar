<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeuanganTagihan extends Model
{
    protected $table = 'keuangan_tagihan';

    protected $fillable = [
        'kode_tagihan', 'iuran_id', 'pemilik', 'keluarga_id', 'rt_id', 'periode',
        'nominal', 'total_dibayar', 'tunggakan', 'status',
        'jatuh_tempo', 'tgl_bayar_lunas', 'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'total_dibayar' => 'decimal:2',
            'tunggakan' => 'decimal:2',
            'jatuh_tempo' => 'date',
            'tgl_bayar_lunas' => 'date',
        ];
    }

    // ============ RELASI ============
    public function iuran() { return $this->belongsTo(KeuanganIuran::class, 'iuran_id'); }
    public function keluarga() { return $this->belongsTo(Keluarga::class); }
    public function rt() { return $this->belongsTo(Rt::class); }
    public function pembayarans() { return $this->hasMany(KeuanganPembayaran::class, 'tagihan_id'); }

    // ============ SCOPE ============
    public function scopePemilikRw($query)
{
    return $query->where('pemilik', 'rw');
}

public function scopePemilikRt($query, $rtId)
{
    return $query->where('pemilik', 'rt')->where('rt_id', $rtId);
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

    // ============ AUTO KODE ============
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($t) {
            if (empty($t->kode_tagihan)) {
                $t->kode_tagihan = static::generateKode();
            }
            $t->tunggakan = $t->nominal - $t->total_dibayar;
        });

        static::updating(function ($t) {
            $t->tunggakan = $t->nominal - $t->total_dibayar;
        });
    }

    protected static function generateKode()
    {
        $periode = now()->format('Ym');
        $prefix = 'INV-' . $periode . '-';

        $last = static::where('kode_tagihan', 'like', $prefix . '%')
            ->orderBy('kode_tagihan', 'desc')->first();

        $nomor = $last ? ((int) substr($last->kode_tagihan, -4)) + 1 : 1;

        return $prefix . str_pad($nomor, 4, '0', STR_PAD_LEFT);
    }

    // ============ ACCESSOR ============
    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'belum_bayar' => 'bg-danger',
            'sebagian' => 'bg-warning text-dark',
            'lunas' => 'bg-success',
            'telat' => 'bg-dark',
            'batal' => 'bg-secondary',
            default => 'bg-secondary',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'belum_bayar' => 'Belum Bayar',
            'sebagian' => 'Sebagian',
            'lunas' => 'Lunas',
            'telat' => 'Telat',
            'batal' => 'Batal',
            default => $this->status,
        };
    }

    public function isTelat(): bool
    {
        return $this->status !== 'lunas'
            && $this->jatuh_tempo
            && now()->gt($this->jatuh_tempo);
    }
}
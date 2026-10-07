<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BansosPenyaluran extends Model
{
    protected $table = 'bansos_penyaluran';

    protected $fillable = [
        'kode_penyaluran', 'penerima_id', 'program_id',
        'tanggal', 'periode', 'nominal', 'jenis_bantuan', 'deskripsi_barang',
        'foto_bukti', 'tanda_tangan', 'status', 'catatan', 'petugas_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'nominal' => 'decimal:2',
        ];
    }

    public function penerima() { return $this->belongsTo(BansosPenerima::class, 'penerima_id'); }
    public function program() { return $this->belongsTo(BansosProgram::class, 'program_id'); }
    public function petugas() { return $this->belongsTo(User::class, 'petugas_id'); }

    // Auto kode
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($p) {
            if (empty($p->kode_penyaluran)) {
                $p->kode_penyaluran = static::generateKode();
            }
        });
    }

    protected static function generateKode()
    {
        $tanggal = now()->format('Ymd');
        $prefix = 'SLY-' . $tanggal . '-';

        $last = static::where('kode_penyaluran', 'like', $prefix . '%')
            ->orderBy('kode_penyaluran', 'desc')->first();

        $nomor = $last ? ((int) substr($last->kode_penyaluran, -3)) + 1 : 1;

        return $prefix . str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'dijadwalkan' => 'bg-warning text-dark',
            'disalurkan' => 'bg-success',
            'dibatalkan' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getFotoBuktiUrlAttribute()
    {
        return $this->foto_bukti ? asset('storage/' . $this->foto_bukti) : null;
    }
}
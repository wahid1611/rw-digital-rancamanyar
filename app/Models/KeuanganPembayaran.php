<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeuanganPembayaran extends Model
{
    protected $table = 'keuangan_pembayaran';

    protected $fillable = [
        'kode_pembayaran', 'tagihan_id', 'keluarga_id', 'nominal',
        'metode', 'tgl_bayar', 'no_referensi', 'bukti', 'catatan',
        'status', 'diverifikasi_oleh', 'dicatat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'tgl_bayar' => 'date',
        ];
    }

    public function tagihan() { return $this->belongsTo(KeuanganTagihan::class, 'tagihan_id'); }
    public function keluarga() { return $this->belongsTo(Keluarga::class); }
    public function pencatat() { return $this->belongsTo(User::class, 'dicatat_oleh'); }
    public function verifikator() { return $this->belongsTo(User::class, 'diverifikasi_oleh'); }

    // Auto kode
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($p) {
            if (empty($p->kode_pembayaran)) {
                $p->kode_pembayaran = static::generateKode();
            }
        });
    }

    protected static function generateKode()
    {
        $tanggal = now()->format('Ymd');
        $prefix = 'PAY-' . $tanggal . '-';

        $last = static::where('kode_pembayaran', 'like', $prefix . '%')
            ->orderBy('kode_pembayaran', 'desc')->first();

        $nomor = $last ? ((int) substr($last->kode_pembayaran, -3)) + 1 : 1;

        return $prefix . str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }

    public function getMetodeLabelAttribute()
{
    return match ($this->metode) {
        'tunai' => 'Tunai',
        'transfer' => 'Transfer',
        'qris' => 'QRIS',
        default => $this->metode,
    };
}

    public function getBuktiUrlAttribute()
    {
        return $this->bukti ? asset('storage/' . $this->bukti) : null;
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeuanganKas extends Model
{
    protected $table = 'keuangan_kas';

    protected $fillable = [
        'kode_transaksi', 'jenis', 'pemilik', 'pemilik_rt_id',
        'kategori', 'nominal', 'tgl_transaksi', 'deskripsi', 'bukti',
        'pembayaran_id', 'dicatat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'tgl_transaksi' => 'date',
        ];
    }

    // ============ RELASI ============
    public function pembayaran() { return $this->belongsTo(KeuanganPembayaran::class, 'pembayaran_id'); }
    public function pencatat() { return $this->belongsTo(User::class, 'dicatat_oleh'); }
    public function rtPemilik() { return $this->belongsTo(Rt::class, 'pemilik_rt_id'); }
    // ============ SCOPE ============
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

    // ============ AUTO KODE ============
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($k) {
            if (empty($k->kode_transaksi)) {
                $k->kode_transaksi = static::generateKode();
            }
        });
    }

    protected static function generateKode()
    {
        $tanggal = now()->format('Ymd');
        $prefix = 'KAS-' . $tanggal . '-';

        $last = static::where('kode_transaksi', 'like', $prefix . '%')
            ->orderBy('kode_transaksi', 'desc')->first();

        $nomor = $last ? ((int) substr($last->kode_transaksi, -3)) + 1 : 1;

        return $prefix . str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }

    // ============ ACCESSOR ============
    public function getJenisBadgeAttribute()
    {
        return $this->jenis === 'pemasukan' ? 'bg-success' : 'bg-danger';
    }

    public function getKategoriLabelAttribute()
    {
        return match ($this->kategori) {
            'iuran' => 'Iuran',
            'sumbangan' => 'Sumbangan',
            'bantuan' => 'Bantuan',
            'denda' => 'Denda',
            'operasional' => 'Operasional',
            'perbaikan' => 'Perbaikan',
            'kegiatan' => 'Kegiatan',
            'sosial' => 'Sosial',
            default => 'Lainnya',
        };
    }

    // ============ HELPER ============
    public static function saldoRw()
{
    $masuk = static::pemilikRw()->where('jenis', 'pemasukan')->sum('nominal');
    $keluar = static::pemilikRw()->where('jenis', 'pengeluaran')->sum('nominal');
    return $masuk - $keluar;
}

public static function saldoRt($rtId)
{
    $masuk = static::pemilikRt($rtId)->where('jenis', 'pemasukan')->sum('nominal');
    $keluar = static::pemilikRt($rtId)->where('jenis', 'pengeluaran')->sum('nominal');
    return $masuk - $keluar;

}
}
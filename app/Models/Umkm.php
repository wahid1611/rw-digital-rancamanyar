<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Umkm extends Model
{
    protected $table = 'umkm';

    protected $fillable = [
        'kode_umkm', 'user_id', 'rt_id',
        'nama_usaha', 'kategori', 'deskripsi',
        'no_hp', 'whatsapp', 'email', 'instagram',
        'alamat', 'latitude', 'longitude',
        'logo', 'foto_usaha', 'jam_operasional',
        'status', 'verified_by', 'verified_at', 'catatan_verifikasi',
        'views',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    // Relasi
    public function user() { return $this->belongsTo(User::class); }
    public function rt() { return $this->belongsTo(Rt::class); }
    public function verifikator() { return $this->belongsTo(User::class, 'verified_by'); }
    public function produks() { return $this->hasMany(UmkmProduk::class)->orderBy('urutan'); }

    // Auto kode
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($u) {
            if (empty($u->kode_umkm)) {
                $u->kode_umkm = static::generateKode();
            }
        });
    }

    protected static function generateKode()
    {
        $periode = now()->format('Ym');
        $prefix = 'UMK-' . $periode . '-';

        $last = static::where('kode_umkm', 'like', $prefix . '%')
            ->orderBy('kode_umkm', 'desc')->first();

        $nomor = $last ? ((int) substr($last->kode_umkm, -3)) + 1 : 1;

        return $prefix . str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }

    // Accessor
    public function getKategoriLabelAttribute()
    {
        return match ($this->kategori) {
            'makanan' => 'Makanan',
            'minuman' => 'Minuman',
            'jasa' => 'Jasa',
            'fashion' => 'Fashion',
            'kerajinan' => 'Kerajinan',
            'pertanian' => 'Pertanian',
            'elektronik' => 'Elektronik',
            default => 'Lainnya',
        };
    }

    public function getKategoriColorAttribute()
    {
        return match ($this->kategori) {
            'makanan' => '#f97316',
            'minuman' => '#06b6d4',
            'jasa' => '#8b5cf6',
            'fashion' => '#ec4899',
            'kerajinan' => '#10b981',
            'pertanian' => '#84cc16',
            'elektronik' => '#3b82f6',
            default => '#667eea',
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'pending' => 'bg-warning text-dark',
            'aktif' => 'bg-success',
            'nonaktif' => 'bg-secondary',
            'ditolak' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'Menunggu Verifikasi',
            'aktif' => 'Aktif',
            'nonaktif' => 'Nonaktif',
            'ditolak' => 'Ditolak',
            default => $this->status,
        };
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    public function getFotoUsahaUrlAttribute()
    {
        return $this->foto_usaha ? asset('storage/' . $this->foto_usaha) : null;
    }

    public function getWhatsappUrlAttribute()
    {
        if (!$this->whatsapp) return null;
        $phone = preg_replace('/^0/', '62', $this->whatsapp);
        return 'https://wa.me/' . $phone;
    }

    // Scope
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
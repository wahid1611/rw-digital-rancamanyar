<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tamu extends Model
{
    protected $table = 'tamu';

    protected $fillable = [
        'kode_tamu', 'qr_code',
        'nama', 'no_hp', 'no_identitas', 'jenis_identitas',
        'alamat_asal', 'instansi',
        'tujuan_tipe', 'tujuan_user_id', 'tujuan_keluarga_id', 'tujuan_rt_id',
        'keperluan',
        'waktu_masuk', 'waktu_keluar',
        'foto_tamu', 'foto_ktp',
        'jenis_kendaraan', 'plat_nomor',
        'status',
        'petugas_id', 'catatan',
        'catatan',
        'catatan_keluar',
    ];

    protected function casts(): array
    {
        return [
            'waktu_masuk' => 'datetime',
            'waktu_keluar' => 'datetime',
        ];
    }

    // Relasi
    public function tujuanUser() { return $this->belongsTo(User::class, 'tujuan_user_id'); }
    public function tujuanKeluarga() { return $this->belongsTo(Keluarga::class, 'tujuan_keluarga_id'); }
    public function tujuanRt() { return $this->belongsTo(Rt::class, 'tujuan_rt_id'); }
    public function petugas() { return $this->belongsTo(User::class, 'petugas_id'); }

    // Auto kode & QR
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($t) {
            if (empty($t->kode_tamu)) {
                $t->kode_tamu = static::generateKode();
            }
            if (empty($t->qr_code)) {
                $t->qr_code = strtoupper(Str::random(16));
            }
        });
    }

    protected static function generateKode()
    {
        $tanggal = now()->format('Ymd');
        $prefix = 'TMU-' . $tanggal . '-';

        $last = static::where('kode_tamu', 'like', $prefix . '%')
            ->orderBy('kode_tamu', 'desc')->first();

        $nomor = $last ? ((int) substr($last->kode_tamu, -3)) + 1 : 1;

        return $prefix . str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }

    // Accessor
    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'masuk' => 'bg-warning text-dark',
            'keluar' => 'bg-success',
            'tidak_kembali' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getTujuanLabelAttribute()
    {
        if ($this->tujuan_tipe === 'warga' && $this->tujuanUser) {
            return 'Warga: ' . $this->tujuanUser->name;
        }
        if ($this->tujuan_tipe === 'rt' && $this->tujuanRt) {
            return 'RT ' . $this->tujuanRt->nomor_rt;
        }
        return match ($this->tujuan_tipe) {
            'rw' => 'Ketua RW',
            'umum' => 'Umum / Fasilitas RW',
            default => 'Lainnya',
        };
    }

    public function getDurasiKunjunganAttribute()
{
    // Hitung durasi dalam detik (integer)
    if (!$this->waktu_keluar) {
        // Masih di dalam - hitung dari waktu masuk sampai sekarang
        $detik = $this->waktu_masuk->diffInSeconds(now());
        $suffix = ' (masih di dalam)';
    } else {
        // Sudah keluar - hitung dari masuk sampai keluar
        $detik = $this->waktu_masuk->diffInSeconds($this->waktu_keluar);
        $suffix = '';
    }

    // Konversi ke format menit / jam yang rapi
    if ($detik < 60) {
        return $detik . ' detik' . $suffix;
    } elseif ($detik < 3600) {
        $menit = floor($detik / 60);
        return $menit . ' menit' . $suffix;
    } else {
        $jam = floor($detik / 3600);
        $sisaMenit = floor(($detik % 3600) / 60);
        return $jam . ' jam ' . $sisaMenit . ' menit' . $suffix;
    }
}

    public function getFotoTamuUrlAttribute()
    {
        return $this->foto_tamu ? asset('storage/' . $this->foto_tamu) : null;
    }

    public function getFotoKtpUrlAttribute()
    {
        return $this->foto_ktp ? asset('storage/' . $this->foto_ktp) : null;
    }
}
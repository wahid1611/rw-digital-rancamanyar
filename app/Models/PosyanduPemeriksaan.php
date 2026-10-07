<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosyanduPemeriksaan extends Model
{
    protected $table = 'posyandu_pemeriksaan';

    protected $fillable = [
        'jadwal_id', 'warga_id', 'rt_id', 'kategori',
        'berat_badan', 'tinggi_badan', 'lingkar_kepala', 'lingkar_lengan', 'status_gizi',
        'tekanan_sistolik', 'tekanan_diastolik', 'gula_darah', 'kolesterol', 'asam_urat', 'suhu_tubuh',
        'imunisasi', 'vitamin',
        'keluhan', 'tindakan', 'catatan', 'petugas_id',
    ];

    protected function casts(): array
    {
        return [
            'berat_badan' => 'decimal:2',
            'tinggi_badan' => 'decimal:2',
            'lingkar_kepala' => 'decimal:2',
            'lingkar_lengan' => 'decimal:2',
            'suhu_tubuh' => 'decimal:1',
        ];
    }

    public function jadwal() { return $this->belongsTo(PosyanduJadwal::class, 'jadwal_id'); }
    public function warga() { return $this->belongsTo(Warga::class); }
    public function rt() { return $this->belongsTo(Rt::class); }
    public function petugas() { return $this->belongsTo(User::class, 'petugas_id'); }

    // Accessor
    public function getKategoriLabelAttribute()
    {
        return match ($this->kategori) {
            'balita' => 'Balita',
            'lansia' => 'Lansia',
            'ibu_hamil' => 'Ibu Hamil',
            default => 'Umum',
        };
    }

    public function getStatusGiziBadgeAttribute()
    {
        return match ($this->status_gizi) {
            'normal' => 'bg-success',
            'kurang' => 'bg-warning text-dark',
            'buruk' => 'bg-danger',
            'lebih' => 'bg-info text-dark',
            'obesitas' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getStatusGiziLabelAttribute()
    {
        return match ($this->status_gizi) {
            'normal' => 'Normal',
            'kurang' => 'Kurang',
            'buruk' => 'Buruk',
            'lebih' => 'Lebih',
            'obesitas' => 'Obesitas',
            default => '-',
        };
    }

    // Helper: hitung IMT (Indeks Massa Tubuh)
    public function getImtAttribute()
    {
        if (!$this->berat_badan || !$this->tinggi_badan || $this->tinggi_badan == 0) return null;
        $tinggiM = $this->tinggi_badan / 100;
        return round($this->berat_badan / ($tinggiM * $tinggiM), 2);
    }

    // Helper: cek tekanan darah
    public function getTekananDarahKategoriAttribute()
    {
        if (!$this->tekanan_sistolik || !$this->tekanan_diastolik) return null;
        $s = $this->tekanan_sistolik;
        $d = $this->tekanan_diastolik;

        if ($s < 90 || $d < 60) return ['label' => 'Rendah', 'badge' => 'bg-info text-dark'];
        if ($s < 120 && $d < 80) return ['label' => 'Normal', 'badge' => 'bg-success'];
        if ($s < 140 || $d < 90) return ['label' => 'Pre-Hipertensi', 'badge' => 'bg-warning text-dark'];
        return ['label' => 'Hipertensi', 'badge' => 'bg-danger'];
    }
}
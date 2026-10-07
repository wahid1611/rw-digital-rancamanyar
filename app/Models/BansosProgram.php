<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BansosProgram extends Model
{
    protected $table = 'bansos_program';

    protected $fillable = [
        'kode_program', 'nama', 'kategori', 'deskripsi', 'sumber',
        'jenis_bantuan', 'nominal', 'satuan_bantuan',
        'tanggal_mulai', 'tanggal_selesai', 'periode', 'kriteria',
        'status', 'dibuat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function pembuat() { return $this->belongsTo(User::class, 'dibuat_oleh'); }
    public function penerimas() { return $this->hasMany(BansosPenerima::class, 'program_id'); }
    public function penyalurans() { return $this->hasMany(BansosPenyaluran::class, 'program_id'); }

    // Auto kode
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($p) {
            if (empty($p->kode_program)) {
                $p->kode_program = static::generateKode();
            }
        });
    }

    protected static function generateKode()
    {
        $tahun = now()->format('Y');
        $prefix = 'BNS-' . $tahun . '-';

        $last = static::where('kode_program', 'like', $prefix . '%')
            ->orderBy('kode_program', 'desc')->first();

        $nomor = $last ? ((int) substr($last->kode_program, -3)) + 1 : 1;

        return $prefix . str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }

    // Accessor
    public function getKategoriLabelAttribute()
    {
        return match ($this->kategori) {
            'blt' => 'BLT (Bantuan Langsung Tunai)',
            'pkh' => 'PKH (Program Keluarga Harapan)',
            'bpnt' => 'BPNT (Bantuan Pangan Non Tunai)',
            'sembako' => 'Sembako',
            'kesehatan' => 'Kesehatan',
            'pendidikan' => 'Pendidikan',
            'bencana' => 'Bantuan Bencana',
            default => 'Lainnya',
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'draft' => 'bg-secondary',
            'aktif' => 'bg-success',
            'selesai' => 'bg-info text-dark',
            'batal' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getJenisBantuanLabelAttribute()
    {
        return match ($this->jenis_bantuan) {
            'uang' => 'Uang',
            'barang' => 'Barang',
            'jasa' => 'Jasa',
            'campuran' => 'Campuran',
            default => $this->jenis_bantuan,
        };
    }
}
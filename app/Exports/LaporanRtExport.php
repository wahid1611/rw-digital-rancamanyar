<?php

namespace App\Exports;

use App\Models\KeuanganKas;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class LaporanRtExport implements FromArray, WithHeadings, WithColumnWidths
{
    protected $rtId;
    protected $dari;
    protected $sampai;

    public function __construct($rtId, $dari, $sampai)
    {
        $this->rtId = $rtId;
        $this->dari = $dari;
        $this->sampai = $sampai;
    }

    public function array(): array
    {
        $data = [];
        $no = 1;
        $items = KeuanganKas::with('pencatat')
            ->where('pemilik', 'rt')
            ->where('pemilik_rt_id', $this->rtId)
            ->whereBetween('tgl_transaksi', [$this->dari, $this->sampai])
            ->orderBy('tgl_transaksi')
            ->get();

        foreach ($items as $kas) {
            $data[] = [
                $no++,
                $kas->tgl_transaksi->format('d/m/Y'),
                $kas->kode_transaksi,
                ucfirst(str_replace('_', ' ', $kas->kategori)),
                $kas->deskripsi,
                $kas->pencatat->name ?? '-',
                $kas->jenis === 'pemasukan' ? $kas->nominal : null,
                $kas->jenis === 'pengeluaran' ? $kas->nominal : null,
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return ['No', 'Tanggal', 'Kode', 'Kategori', 'Deskripsi', 'Dicatat Oleh', 'Masuk (Rp)', 'Keluar (Rp)'];
    }

    public function columnWidths(): array
    {
        return ['A' => 6, 'B' => 12, 'C' => 20, 'D' => 15, 'E' => 40, 'F' => 20, 'G' => 15, 'H' => 15];
    }
}
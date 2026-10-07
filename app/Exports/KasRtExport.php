<?php

namespace App\Exports;

use App\Models\KeuanganKas;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class KasRtExport implements FromArray, WithHeadings, WithColumnWidths
{
    protected $rtId;
    protected $tab;

    public function __construct($rtId, $tab = null)
    {
        $this->rtId = $rtId;
        $this->tab = $tab;
    }

    public function array(): array
    {
        $query = KeuanganKas::with('pencatat')
            ->where('pemilik', 'rt')
            ->where('pemilik_rt_id', $this->rtId)
            ->orderBy('tgl_transaksi', 'desc');

        if ($this->tab === 'masuk') {
            $query->where('jenis', 'pemasukan');
        } elseif ($this->tab === 'keluar') {
            $query->where('jenis', 'pengeluaran');
        }

        $data = [];
        $no = 1;
        foreach ($query->get() as $kas) {
            $data[] = [
                $no++,
                $kas->tgl_transaksi->format('d/m/Y'),
                $kas->kode_transaksi,
                ucfirst(str_replace('_', ' ', $kas->kategori)),
                $kas->jenis === 'pemasukan' ? 'Masuk' : 'Keluar',
                $kas->deskripsi,
                $kas->nominal,
                $kas->pencatat->name ?? '-',
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return ['No', 'Tanggal', 'Kode Transaksi', 'Kategori', 'Jenis', 'Deskripsi', 'Nominal (Rp)', 'Dicatat Oleh'];
    }

    public function columnWidths(): array
    {
        return ['A' => 6, 'B' => 12, 'C' => 20, 'D' => 15, 'E' => 10, 'F' => 50, 'G' => 15, 'H' => 20];
    }
}
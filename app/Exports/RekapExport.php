<?php

namespace App\Exports;

use App\Models\KeuanganKas;
use App\Models\KeuanganTagihan;
use App\Models\Rt;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class RekapExport implements FromArray, WithHeadings, WithColumnWidths
{
    public function array(): array
    {
        $rts = Rt::orderBy('nomor_rt')->get();
        $data = [];
        $no = 1;

        foreach ($rts as $rt) {
            $pemasukan = KeuanganKas::pemilikRt($rt->id)->where('jenis', 'pemasukan')->sum('nominal');
            $pengeluaran = KeuanganKas::pemilikRt($rt->id)->where('jenis', 'pengeluaran')->sum('nominal');
            $tunggakan = KeuanganTagihan::pemilikRt($rt->id)
                ->whereIn('status', ['belum_bayar', 'sebagian', 'telat'])
                ->sum('tunggakan');

            $data[] = [
                $no++,
                $rt->nama_rt,
                $pemasukan,
                $pengeluaran,
                $pemasukan - $pengeluaran,
                $tunggakan,
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return ['No', 'RT', 'Uang Masuk (Rp)', 'Uang Keluar (Rp)', 'Saldo (Rp)', 'Tunggakan (Rp)'];
    }

    public function columnWidths(): array
    {
        return ['A' => 6, 'B' => 15, 'C' => 20, 'D' => 20, 'E' => 20, 'F' => 20];
    }
}
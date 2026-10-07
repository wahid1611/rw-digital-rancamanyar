<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache permission
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ============ DAFTAR PERMISSION ============
        $modul = [
            'warga'         => ['view', 'create', 'edit', 'delete', 'export'],
            'keluarga'      => ['view', 'create', 'edit', 'delete', 'export'],
            'pengumuman' => ['view', 'create', 'edit', 'delete', 'publish'],
            'berita'     => ['view', 'create', 'edit', 'delete', 'publish'],
            'kalender'      => ['view', 'create', 'edit', 'delete'],
            'surat'         => ['view', 'create', 'edit', 'delete', 'approve', 'sign', 'export'],
            'keuangan'      => ['view', 'create', 'edit', 'delete', 'approve', 'export'],
            'iuran'         => ['view', 'create', 'edit', 'delete', 'generate'],
            'tagihan'       => ['view', 'create', 'edit', 'delete', 'bayar', 'generate'],
            'kas'           => ['view', 'create', 'edit', 'delete', 'export'],
            'pengaduan'     => ['view', 'create', 'edit', 'delete', 'handle', 'export'],
            'ronda'         => ['view', 'create', 'edit', 'delete', 'schedule', 'absen', 'lapor'],
            'panic'         => ['view', 'create', 'handle'],
            'tamu'          => ['view', 'create', 'edit', 'delete', 'checkout', 'export'],
            'inventaris'    => ['view', 'create', 'edit', 'delete', 'pinjam', 'approve', 'kembalikan'],
            'umkm'          => ['view', 'create', 'edit', 'delete', 'verifikasi'],
            'lowongan'      => ['view', 'create', 'edit', 'delete', 'lamar', 'verifikasi', 'teruskan'],
            'posyandu'      => ['view', 'create', 'edit', 'delete', 'periksa', 'export'],
            'bansos'        => ['view', 'create', 'edit', 'delete', 'verifikasi', 'salurkan', 'export'],
            'peta'          => ['view', 'create', 'edit', 'delete'],
            'akta'          => ['view', 'create', 'edit', 'delete'],
            'user'          => ['view', 'create', 'edit', 'delete', 'reset_password'],
            'role'          => ['view', 'create', 'edit', 'delete'],
            'dashboard'     => ['view_rw', 'view_rt', 'view_keuangan', 'view_security'],
            'keuangan_rw'   => ['view', 'create', 'edit', 'delete', 'approve', 'export', 'import', 'setting'],
            'keuangan_rt'   => ['view', 'create', 'edit', 'delete', 'export', 'import'],
            // === REKAP KEUANGAN (khusus Ketua RW) ===
            'keuangan_rekap' => ['view', 'export'],
             ];

        foreach ($modul as $nama => $aksis) {
            foreach ($aksis as $aksi) {
                Permission::firstOrCreate(['name' => "{$nama}.{$aksi}"]);
            }
        }

        // ============ DAFTAR ROLE ============
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        // Super admin otomatis punya semua permission via Gate::before (lihat langkah 5)
        $superAdmin->givePermissionTo(Permission::all());

        $ketuaRw = Role::firstOrCreate(['name' => 'ketua_rw']);
        $ketuaRw->givePermissionTo(Permission::all());

        $ketuaRt = Role::firstOrCreate(['name' => 'ketua_rt']);
        $ketuaRt->givePermissionTo([
            'dashboard.view_rt',
            'warga.view', 'warga.create', 'warga.edit', 'warga.export',
            'keluarga.view', 'keluarga.create', 'keluarga.edit',
            'pengumuman.view', 'pengumuman.create', 'pengumuman.edit',
            'berita.view', 'kalender.view',
            'surat.view', 'surat.approve',
            'pengaduan.view', 'pengaduan.create', 'pengaduan.handle',
            'ronda.view', 'ronda.schedule',
            'panic.view', 'panic.create', 'panic.handle',
            'tamu.view','tamu.create', 'tamu.edit', 'tamu.checkout',
            'inventaris.view', 'inventaris.create', 'inventaris.edit',
            'inventaris.pinjam', 'inventaris.approve', 'inventaris.kembalikan',
            'umkm.view', 'umkm.verifikasi',
            'lowongan.view', 'lowongan.verifikasi', 'lowongan.teruskan',
            'posyandu.view',
            'bansos.view', 'bansos.verifikasi',
            'peta.view', 'peta.create', 'peta.edit',
            'akta.view', 'akta.create', 'akta.edit',
            'tagihan.view',  // Lihat tagihan RT-nya
        ]);

        $sekretaris = Role::firstOrCreate(['name' => 'sekretaris']);
        $sekretaris->givePermissionTo([
            'dashboard.view_rw',
            'warga.view', 'warga.create', 'warga.edit', 'warga.export',
            'keluarga.view', 'keluarga.create', 'keluarga.edit', 'keluarga.export',
            'pengumuman.view', 'pengumuman.create', 'pengumuman.edit',
            'berita.view', 'berita.create', 'berita.edit',
            'kalender.view', 'kalender.create', 'kalender.edit',
            'surat.view', 'surat.create', 'surat.edit', 'surat.approve',
            'pengaduan.view',
            'panic.view', 'panic.create',
            'inventaris.view', 'inventaris.create', 'inventaris.edit',
            'umkm.view', 'umkm.verifikasi',
            'lowongan.view',
            'posyandu.view',
            'bansos.view', 'bansos.create', 'bansos.edit', 'bansos.verifikasi',
            'peta.view', 'peta.create', 'peta.edit',
            'akta.view', 'akta.create', 'akta.edit',
        ]);

        $bendahara = Role::firstOrCreate(['name' => 'bendahara']);
        $bendahara->givePermissionTo([
        'dashboard.view_keuangan',
        'keuangan_rw.view', 'keuangan_rw.create', 'keuangan_rw.edit', 'keuangan_rw.delete',
        'keuangan_rw.approve', 'keuangan_rw.export', 'keuangan_rw.import', 'keuangan_rw.setting',
        'iuran.view', 'iuran.create', 'iuran.edit', 'iuran.delete', 'iuran.generate',
        'tagihan.view', 'tagihan.create', 'tagihan.edit', 'tagihan.delete', 'tagihan.bayar', 'tagihan.generate',
        'kas.view', 'kas.create', 'kas.edit', 'kas.delete', 'kas.export',
        'warga.view',
        'pengumuman.view',
        'inventaris.view',
        ]);

        // === BENDahara RT ===
        $bendaharaRt = Role::firstOrCreate(['name' => 'bendahara_rt']);
        $bendaharaRt->givePermissionTo([
        'dashboard.view_rt',
        'keuangan_rt.view', 'keuangan_rt.create', 'keuangan_rt.edit',
        'keuangan_rt.delete', 'keuangan_rt.export', 'keuangan_rt.import',
        'iuran.view', 'iuran.create', 'iuran.edit', 'iuran.generate',
        'tagihan.view', 'tagihan.create', 'tagihan.edit', 'tagihan.bayar', 'tagihan.generate',
        'kas.view', 'kas.create', 'kas.edit', 'kas.export',
        'warga.view', 'pengumuman.view', 'inventaris.view',
        ]);

        $sesepuh = Role::firstOrCreate(['name' => 'sesepuh']);
        $sesepuh->givePermissionTo([
            'dashboard.view_rw',
            'warga.view', 'keluarga.view',
            'pengumuman.view', 'berita.view', 'kalender.view',
            'surat.view', 'keuangan.view', 'pengaduan.view',
            'ronda.view', 'tamu.view', 'inventaris.view',
            'umkm.view', 'lowongan.view', 'posyandu.view',
            'bansos.view', 'peta.view', 'akta.view',
            'panic.view', 'panic.create',
        ]);

        $security = Role::firstOrCreate(['name' => 'security']);
        $security->givePermissionTo([
            'dashboard.view_security',
            'tamu.view', 'tamu.create', 'tamu.edit', 'tamu.checkout',
            'ronda.view', 'ronda.schedule', 'ronda.absen', 'ronda.lapor',
            'panic.view', 'panic.handle',
            'pengaduan.view', 'pengaduan.create',
        ]);

        $posyandu = Role::firstOrCreate(['name' => 'posyandu']);
        $posyandu->givePermissionTo([
            'dashboard.view_rw',
            'warga.view',
            'posyandu.view', 'posyandu.create', 'posyandu.edit', 'posyandu.periksa',
            'pengumuman.view',
            'peta.view',
            'panic.view', 'panic.create',
        ]);

        $warga = Role::firstOrCreate(['name' => 'warga']);
        $warga->givePermissionTo([
            'pengumuman.view', 'berita.view', 'kalender.view',
            'surat.view', 'surat.create',
            'pengaduan.view', 'pengaduan.create',
            'umkm.view', 'umkm.create',
            'lowongan.view', 'lowongan.lamar',
            'inventaris.view',
            'peta.view',
            'tagihan.view',  // Lihat tagihan sendiri (nanti filter di controller)
            'panic.view', 'panic.create',
            'tamu.view', 'tamu.create',  // Warga bisa lihat & catat tamu
            'bansos.view',
            'tagihan.view',
        ]);

        $this->command->info('✅ Role & Permission berhasil di-seed.');
    }
}
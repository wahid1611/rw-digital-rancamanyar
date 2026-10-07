# RW Digital - Rancamanyar RW 07

## Setup Lokal
1. Clone/copy project ke `C:\laragon\www\`
2. `composer install`
3. `npm install && npm run build`
4. Copy `.env.example` → `.env`
5. `php artisan key:generate`
6. Buat database `rw_digital` di MySQL
7. Import file `.sql`
8. `php artisan config:clear`
9. Akses: http://rancamanyar-rw07-main.test

## Login Default
- Super Admin: [email] / [password]
- Ketua RT: [email] / [password]

## Modul yang Tersedia
1. Fondasi (Auth, User, Role, Permission)
2. Data Warga & Keluarga
3. Pengumuman & Berita
4. Pengaduan + Eskalasi
5. Surat Menyurat + PDF + QR
6. Security & Ronda + Panic Button
7. Inventaris + Peminjaman
8. Lowongan Kerja + Lamaran
9. Peta Digital
10. Kelahiran & Kematian
11. Keuangan RW/RT
12. Manajemen Tamu
13. UMKM Warga
14. Posyandu
15. Bantuan Sosial

## Kontributor
- [Andrianto Tri Saputra] - Developer awal
- [Wahid Maulana F.] - lanjutan 5 Modul : Keuangan RW/RT, Manajemen Tamu, UMKM Warga, Posyandu, Bantuan Sosial
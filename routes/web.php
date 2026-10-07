<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KeluargaController;
use App\Http\Controllers\WargaController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\JenisSuratController;
use App\Http\Controllers\SuratPdfController;
use App\Http\Controllers\JadwalRondaController;
use App\Http\Controllers\PanicButtonController;
use App\Http\Controllers\AsetController;
use App\Http\Controllers\PeminjamanAsetController;
use App\Http\Controllers\LowonganController;
use App\Http\Controllers\LamaranController;
use App\Http\Controllers\PetaController;
use App\Http\Controllers\KelahiranController;
use App\Http\Controllers\KematianController;
//use App\Http\Controllers\KeuanganDashboardController;
//use App\Http\Controllers\KeuanganIuranController;
//use App\Http\Controllers\KeuanganTagihanController;
//use App\Http\Controllers\KeuanganPembayaranController;
//use App\Http\Controllers\KeuanganKasController;
use App\Http\Controllers\TamuController;
use App\Http\Controllers\PosyanduJadwalController;
use App\Http\Controllers\PosyanduController;
//use App\Http\Controllers\PosyanduPemeriksaanController;
use App\Http\Controllers\UmkmController;
use App\Http\Controllers\UmkmProdukController;
use App\Http\Controllers\BansosProgramController;
use App\Http\Controllers\BansosPenerimaController;
use App\Http\Controllers\BansosPenyaluranController;
use App\Http\Controllers\KeuanganRwController;
use App\Http\Controllers\KeuanganRtController;
use App\Http\Controllers\KeuanganRekapController;
use App\Http\Controllers\KeuanganWargaController;
use App\Http\Controllers\PengaturanPembayaranController;

// ============ GUEST ROUTES ============
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// ============ VERIFIKASI QR (PUBLIK — tanpa login) ============
Route::get('/verifikasi/{qrCode}', [SuratPdfController::class, 'verifikasi'])->name('surat.verifikasi');

// ============ AUTH ROUTES ============
Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // ============ PASSWORD ============
    Route::get('/password/wajib', [PasswordController::class, 'showWajibForm'])->name('password.wajib');
    Route::post('/password/wajib', [PasswordController::class, 'updateWajib'])->name('password.wajib.update');
    Route::get('/password/ganti', [PasswordController::class, 'showGantiForm'])->name('password.ganti');
    Route::post('/password/ganti', [PasswordController::class, 'updateGanti'])->name('password.ganti.update');

    // ============ DASHBOARD ============
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ============ FILE PREVIEW ============
    Route::get('/file/preview/{path}', function ($path) {
        $fullPath = storage_path('app/public/' . $path);
        if (!file_exists($fullPath)) {
            abort(404);
        }

        $mime = mime_content_type($fullPath);

        if ($mime === 'application/pdf') {
            $pdf = base64_encode(file_get_contents($fullPath));
            return view('preview-pdf', compact('pdf', 'path'));
        }

        return response(file_get_contents($fullPath), 200)
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', 'inline; filename="' . basename($fullPath) . '"');
    })->where('path', '.*')->name('file.preview');

    // ============ USER MANAGEMENT ============
    Route::prefix('users')->name('users.')->group(function () {
        Route::middleware('permission:user.view')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
        });
        Route::middleware('permission:user.create')->group(function () {
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
        });
        Route::middleware('permission:user.view')->group(function () {
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
        });
        Route::middleware('permission:user.edit')->group(function () {
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        });
        Route::middleware('permission:user.delete')->group(function () {
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ ROLE MANAGEMENT ============
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::middleware('permission:role.view')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
        });
        Route::middleware('permission:role.create')->group(function () {
            Route::get('/create', [RoleController::class, 'create'])->name('create');
            Route::post('/', [RoleController::class, 'store'])->name('store');
        });
        Route::middleware('permission:role.view')->group(function () {
            Route::get('/{role}', [RoleController::class, 'show'])->name('show');
        });
        Route::middleware('permission:role.edit')->group(function () {
            Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
            Route::put('/{role}', [RoleController::class, 'update'])->name('update');
        });
        Route::middleware('permission:role.delete')->group(function () {
            Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ KELUARGA ============
    Route::prefix('keluarga')->name('keluarga.')->group(function () {
        Route::middleware('permission:keluarga.view')->group(function () {
            Route::get('/', [KeluargaController::class, 'index'])->name('index');
        });
        Route::middleware('permission:keluarga.create')->group(function () {
            Route::get('/create', [KeluargaController::class, 'create'])->name('create');
            Route::post('/', [KeluargaController::class, 'store'])->name('store');
        });
        Route::middleware('permission:keluarga.view')->group(function () {
            Route::get('/{keluarga}', [KeluargaController::class, 'show'])->name('show');
        });
        Route::middleware('permission:keluarga.edit')->group(function () {
            Route::get('/{keluarga}/edit', [KeluargaController::class, 'edit'])->name('edit');
            Route::put('/{keluarga}', [KeluargaController::class, 'update'])->name('update');
        });
        Route::middleware('permission:keluarga.delete')->group(function () {
            Route::delete('/{keluarga}', [KeluargaController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ WARGA ============
    Route::prefix('warga')->name('warga.')->group(function () {
        Route::middleware('permission:warga.view')->group(function () {
            Route::get('/', [WargaController::class, 'index'])->name('index');
        });
        Route::middleware('permission:warga.create')->group(function () {
            Route::get('/create', [WargaController::class, 'create'])->name('create');
            Route::post('/', [WargaController::class, 'store'])->name('store');
        });
        Route::middleware('permission:warga.view')->group(function () {
            Route::get('/{warga}', [WargaController::class, 'show'])->name('show');
        });
        Route::middleware('permission:warga.edit')->group(function () {
            Route::get('/{warga}/edit', [WargaController::class, 'edit'])->name('edit');
            Route::put('/{warga}', [WargaController::class, 'update'])->name('update');
        });
        Route::middleware('permission:warga.delete')->group(function () {
            Route::delete('/{warga}', [WargaController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ PENGUMUMAN & BERITA ============
    Route::prefix('pengumuman')->name('pengumuman.')->group(function () {
        Route::middleware('permission:pengumuman.view')->group(function () {
            Route::get('/', [PengumumanController::class, 'index'])->name('index');
        });
        Route::middleware('permission:pengumuman.create')->group(function () {
            Route::get('/create', [PengumumanController::class, 'create'])->name('create');
            Route::post('/', [PengumumanController::class, 'store'])->name('store');
        });
        Route::middleware('permission:pengumuman.view')->group(function () {
            Route::get('/{pengumuman}', [PengumumanController::class, 'show'])->name('show');
        });
        Route::middleware('permission:pengumuman.edit')->group(function () {
            Route::get('/{pengumuman}/edit', [PengumumanController::class, 'edit'])->name('edit');
            Route::put('/{pengumuman}', [PengumumanController::class, 'update'])->name('update');
        });
        Route::middleware('permission:pengumuman.delete')->group(function () {
            Route::delete('/{pengumuman}', [PengumumanController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ PENGADUAN ============
    Route::prefix('pengaduan')->name('pengaduan.')->group(function () {
        Route::middleware('permission:pengaduan.view')->group(function () {
            Route::get('/', [PengaduanController::class, 'index'])->name('index');
        });
        Route::middleware('permission:pengaduan.create')->group(function () {
            Route::get('/create', [PengaduanController::class, 'create'])->name('create');
            Route::post('/', [PengaduanController::class, 'store'])->name('store');
        });
        Route::middleware('permission:pengaduan.view')->group(function () {
            Route::get('/{pengaduan}', [PengaduanController::class, 'show'])->name('show');
        });
        Route::middleware('permission:pengaduan.edit')->group(function () {
            Route::get('/{pengaduan}/edit', [PengaduanController::class, 'edit'])->name('edit');
            Route::put('/{pengaduan}', [PengaduanController::class, 'update'])->name('update');
        });
        Route::middleware('permission:pengaduan.handle')->group(function () {
            Route::post('/{pengaduan}/tindak-lanjut', [PengaduanController::class, 'tindakLanjut'])->name('tindak-lanjut');
            Route::post('/{pengaduan}/eskalasi', [PengaduanController::class, 'eskalasi'])->name('eskalasi');
        });
        Route::middleware('permission:pengaduan.delete')->group(function () {
            Route::delete('/{pengaduan}', [PengaduanController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ SURAT MENYURAT ============
    Route::prefix('surat')->name('surat.')->group(function () {
        Route::middleware('permission:surat.view')->group(function () {
            Route::get('/', [SuratController::class, 'index'])->name('index');
        });
        Route::middleware('permission:surat.create')->group(function () {
            Route::get('/create', [SuratController::class, 'create'])->name('create');
            Route::post('/', [SuratController::class, 'store'])->name('store');
        });
        Route::middleware('permission:surat.view')->group(function () {
            Route::get('/{surat}', [SuratController::class, 'show'])->name('show');
        });
        Route::middleware('permission:surat.edit')->group(function () {
            Route::get('/{surat}/edit', [SuratController::class, 'edit'])->name('edit');
            Route::put('/{surat}', [SuratController::class, 'update'])->name('update');
        });
        Route::middleware('permission:surat.approve')->group(function () {
            Route::post('/{surat}/approve-rt', [SuratController::class, 'approveRt'])->name('approve-rt');
            Route::post('/{surat}/tolak-rt', [SuratController::class, 'tolakRt'])->name('tolak-rt');
            Route::post('/{surat}/approve-rw', [SuratController::class, 'approveRw'])->name('approve-rw');
            Route::post('/{surat}/tolak-rw', [SuratController::class, 'tolakRw'])->name('tolak-rw');
        });
        Route::middleware('permission:surat.delete')->group(function () {
            Route::delete('/{surat}', [SuratController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ JENIS SURAT ============
    Route::prefix('jenis-surat')->name('jenis-surat.')->group(function () {
        Route::middleware('permission:surat.view')->group(function () {
            Route::get('/', [JenisSuratController::class, 'index'])->name('index');
        });
        Route::middleware('permission:surat.create')->group(function () {
            Route::get('/create', [JenisSuratController::class, 'create'])->name('create');
            Route::post('/', [JenisSuratController::class, 'store'])->name('store');
        });
        Route::middleware('permission:surat.view')->group(function () {
            Route::get('/{jenisSurat}', [JenisSuratController::class, 'show'])->name('show');
        });
        Route::middleware('permission:surat.edit')->group(function () {
            Route::get('/{jenisSurat}/edit', [JenisSuratController::class, 'edit'])->name('edit');
            Route::put('/{jenisSurat}', [JenisSuratController::class, 'update'])->name('update');
        });
        Route::middleware('permission:surat.delete')->group(function () {
            Route::delete('/{jenisSurat}', [JenisSuratController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ SURAT PDF ============
    Route::get('/surat/{surat}/pdf', [SuratPdfController::class, 'preview'])->name('surat.pdf.preview');
    Route::get('/surat/{surat}/view', [SuratPdfController::class, 'viewInline'])->name('surat.pdf.view');
    Route::get('/surat/{surat}/download', [SuratPdfController::class, 'download'])->name('surat.pdf.download');

    // ============ JADWAL RONDA ============
    Route::prefix('ronda')->name('ronda.')->group(function () {
        Route::middleware('permission:ronda.view')->group(function () {
            Route::get('/', [JadwalRondaController::class, 'index'])->name('index');
        });
        Route::middleware('permission:ronda.schedule')->group(function () {
            Route::get('/create', [JadwalRondaController::class, 'create'])->name('create');
            Route::post('/', [JadwalRondaController::class, 'store'])->name('store');
            Route::post('/users-by-rt', [JadwalRondaController::class, 'getUsersByRt'])->name('users-by-rt');
        });
        Route::middleware('permission:ronda.view')->group(function () {
            Route::get('/{ronda}', [JadwalRondaController::class, 'show'])->name('show');
        });
        Route::middleware('permission:ronda.edit')->group(function () {
            Route::get('/{ronda}/edit', [JadwalRondaController::class, 'edit'])->name('edit');
            Route::put('/{ronda}', [JadwalRondaController::class, 'update'])->name('update');
        });
        Route::middleware('permission:ronda.absen')->group(function () {
            Route::post('/{ronda}/absen', [JadwalRondaController::class, 'absen'])->name('absen');
        });
        Route::middleware('permission:ronda.lapor')->group(function () {
            Route::post('/{ronda}/lapor', [JadwalRondaController::class, 'lapor'])->name('lapor');
        });
        Route::middleware('permission:ronda.delete')->group(function () {
            Route::delete('/{ronda}', [JadwalRondaController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ PANIC BUTTON ============
    Route::prefix('panic')->name('panic.')->group(function () {
        Route::middleware('permission:panic.view')->group(function () {
            Route::get('/', [PanicButtonController::class, 'index'])->name('index');
        });
        Route::middleware('permission:panic.create')->group(function () {
            Route::get('/create', [PanicButtonController::class, 'create'])->name('create');
            Route::post('/', [PanicButtonController::class, 'store'])->name('store');
        });
        Route::middleware('permission:panic.view')->group(function () {
            Route::get('/{panic}', [PanicButtonController::class, 'show'])->name('show');
        });
        Route::middleware('permission:panic.handle')->group(function () {
            Route::get('/{panic}/edit', [PanicButtonController::class, 'edit'])->name('edit');
            Route::put('/{panic}', [PanicButtonController::class, 'update'])->name('update');
            Route::delete('/{panic}', [PanicButtonController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ INVENTARIS ============
    Route::prefix('inventaris')->name('inventaris.')->group(function () {
        Route::middleware('permission:inventaris.view')->group(function () {
            Route::get('/', [AsetController::class, 'index'])->name('index');
        });
        Route::middleware('permission:inventaris.create')->group(function () {
            Route::get('/create', [AsetController::class, 'create'])->name('create');
            Route::post('/', [AsetController::class, 'store'])->name('store');
        });
        Route::middleware('permission:inventaris.view')->group(function () {
            Route::get('/{inventaris}', [AsetController::class, 'show'])->name('show');
        });
        Route::middleware('permission:inventaris.edit')->group(function () {
            Route::get('/{inventaris}/edit', [AsetController::class, 'edit'])->name('edit');
            Route::put('/{inventaris}', [AsetController::class, 'update'])->name('update');
        });
        Route::middleware('permission:inventaris.delete')->group(function () {
            Route::delete('/{inventaris}', [AsetController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ PEMINJAMAN ASET ============
    Route::prefix('peminjaman-aset')->name('peminjaman-aset.')->group(function () {
        Route::middleware('permission:inventaris.view')->group(function () {
            Route::get('/', [PeminjamanAsetController::class, 'index'])->name('index');
        });
        Route::middleware('permission:inventaris.pinjam')->group(function () {
            Route::get('/create', [PeminjamanAsetController::class, 'create'])->name('create');
            Route::post('/', [PeminjamanAsetController::class, 'store'])->name('store');
        });
        Route::middleware('permission:inventaris.view')->group(function () {
            Route::get('/{peminjamanAset}', [PeminjamanAsetController::class, 'show'])->name('show');
        });
        Route::middleware('permission:inventaris.pinjam')->group(function () {
            Route::get('/{peminjamanAset}/edit', [PeminjamanAsetController::class, 'edit'])->name('edit');
            Route::put('/{peminjamanAset}', [PeminjamanAsetController::class, 'update'])->name('update');
        });
        Route::middleware('permission:inventaris.approve')->group(function () {
            Route::post('/{peminjamanAset}/approve', [PeminjamanAsetController::class, 'approve'])->name('approve');
            Route::post('/{peminjamanAset}/tolak', [PeminjamanAsetController::class, 'tolak'])->name('tolak');
            Route::post('/{peminjamanAset}/tandai-dipinjam', [PeminjamanAsetController::class, 'tandaiDipinjam'])->name('tandai-dipinjam');
        });
        Route::middleware('permission:inventaris.kembalikan')->group(function () {
            Route::post('/{peminjamanAset}/kembalikan', [PeminjamanAsetController::class, 'kembalikan'])->name('kembalikan');
        });
        Route::middleware('permission:inventaris.delete')->group(function () {
            Route::delete('/{peminjamanAset}', [PeminjamanAsetController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ LOWONGAN KERJA ============
    Route::prefix('lowongan')->name('lowongan.')->group(function () {
        Route::middleware('permission:lowongan.view')->group(function () {
            Route::get('/', [LowonganController::class, 'index'])->name('index');
        });
        Route::middleware('permission:lowongan.create')->group(function () {
            Route::get('/create', [LowonganController::class, 'create'])->name('create');
            Route::post('/', [LowonganController::class, 'store'])->name('store');
        });
        Route::middleware('permission:lowongan.view')->group(function () {
            Route::get('/{lowongan}', [LowonganController::class, 'show'])->name('show');
        });
        Route::middleware('permission:lowongan.edit')->group(function () {
            Route::get('/{lowongan}/edit', [LowonganController::class, 'edit'])->name('edit');
            Route::put('/{lowongan}', [LowonganController::class, 'update'])->name('update');
        });
        Route::middleware('permission:lowongan.delete')->group(function () {
            Route::delete('/{lowongan}', [LowonganController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ LAMARAN ============
    Route::prefix('lamaran')->name('lamaran.')->group(function () {
        Route::middleware('permission:lowongan.view')->group(function () {
            Route::get('/', [LamaranController::class, 'index'])->name('index');
        });
        Route::middleware('permission:lowongan.lamar')->group(function () {
            Route::get('/create', [LamaranController::class, 'create'])->name('create');
            Route::post('/', [LamaranController::class, 'store'])->name('store');
        });
        Route::middleware('permission:lowongan.view')->group(function () {
            Route::get('/{lamaran}', [LamaranController::class, 'show'])->name('show');
        });
        Route::middleware('permission:lowongan.lamar')->group(function () {
            Route::get('/{lamaran}/edit', [LamaranController::class, 'edit'])->name('edit');
            Route::put('/{lamaran}', [LamaranController::class, 'update'])->name('update');
        });
        Route::middleware('permission:lowongan.delete')->group(function () {
            Route::delete('/{lamaran}', [LamaranController::class, 'destroy'])->name('destroy');
        });
        Route::middleware('permission:lowongan.verifikasi')->group(function () {
            Route::post('/{lamaran}/verifikasi', [LamaranController::class, 'verifikasi'])->name('verifikasi');
            Route::post('/{lamaran}/status', [LamaranController::class, 'updateStatus'])->name('update-status');
        });
        Route::middleware('permission:lowongan.teruskan')->group(function () {
            Route::post('/{lamaran}/teruskan', [LamaranController::class, 'teruskan'])->name('teruskan');
        });
    });

    // ============ PETA DIGITAL ============
    Route::prefix('peta')->name('peta.')->group(function () {
        Route::middleware('permission:peta.view')->group(function () {
            Route::get('/', [PetaController::class, 'index'])->name('index');
            Route::get('/api/lokasi', [PetaController::class, 'apiLokasi'])->name('api-lokasi');
        });
        Route::middleware('permission:peta.create')->group(function () {
            Route::get('/create', [PetaController::class, 'create'])->name('create');
            Route::post('/', [PetaController::class, 'store'])->name('store');
        });
        Route::middleware('permission:peta.view')->group(function () {
            Route::get('/{lokasi}', [PetaController::class, 'show'])->name('show');
        });
        Route::middleware('permission:peta.edit')->group(function () {
            Route::get('/{lokasi}/edit', [PetaController::class, 'edit'])->name('edit');
            Route::put('/{lokasi}', [PetaController::class, 'update'])->name('update');
        });
        Route::middleware('permission:peta.delete')->group(function () {
            Route::delete('/{lokasi}', [PetaController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ KELAHIRAN ============
    Route::prefix('kelahiran')->name('kelahiran.')->group(function () {
        Route::middleware('permission:akta.view')->group(function () {
            Route::get('/', [KelahiranController::class, 'index'])->name('index');
        });
        Route::middleware('permission:akta.create')->group(function () {
            Route::get('/create', [KelahiranController::class, 'create'])->name('create');
            Route::post('/', [KelahiranController::class, 'store'])->name('store');
        });
        Route::middleware('permission:akta.view')->group(function () {
            Route::get('/{kelahiran}', [KelahiranController::class, 'show'])->name('show');
        });
        Route::middleware('permission:akta.edit')->group(function () {
            Route::get('/{kelahiran}/edit', [KelahiranController::class, 'edit'])->name('edit');
            Route::put('/{kelahiran}', [KelahiranController::class, 'update'])->name('update');
        });
        Route::middleware('permission:akta.delete')->group(function () {
            Route::delete('/{kelahiran}', [KelahiranController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ KEMATIAN ============
    Route::prefix('kematian')->name('kematian.')->group(function () {
        Route::middleware('permission:akta.view')->group(function () {
            Route::get('/', [KematianController::class, 'index'])->name('index');
        });
        Route::middleware('permission:akta.create')->group(function () {
            Route::get('/create', [KematianController::class, 'create'])->name('create');
            Route::post('/', [KematianController::class, 'store'])->name('store');
        });
        Route::middleware('permission:akta.view')->group(function () {
            Route::get('/{kematian}', [KematianController::class, 'show'])->name('show');
        });
        Route::middleware('permission:akta.edit')->group(function () {
            Route::get('/{kematian}/edit', [KematianController::class, 'edit'])->name('edit');
            Route::put('/{kematian}', [KematianController::class, 'update'])->name('update');
        });
        Route::middleware('permission:akta.delete')->group(function () {
            Route::delete('/{kematian}', [KematianController::class, 'destroy'])->name('destroy');
        });
    });

    // ============ KEUANGAN RW ============
    Route::prefix('keuangan/rw')->name('keuangan.rw.')->group(function () {
        Route::middleware('permission:keuangan_rw.view')->group(function () {
            Route::get('/', [KeuanganRwController::class, 'dashboard'])->name('dashboard');
            Route::get('/kas', [KeuanganRwController::class, 'kas'])->name('kas');
            Route::get('/belum-bayar', [KeuanganRwController::class, 'belumBayar'])->name('belum-bayar');
            Route::get('/laporan', [KeuanganRwController::class, 'laporan'])->name('laporan');
        });

        Route::middleware('permission:keuangan_rw.create')->group(function () {
            Route::get('/kas/masuk/create', [KeuanganRwController::class, 'createMasuk'])->name('masuk.create');
            Route::post('/kas/masuk', [KeuanganRwController::class, 'storeMasuk'])->name('masuk.store');
            Route::get('/kas/keluar/create', [KeuanganRwController::class, 'createKeluar'])->name('keluar.create');
            Route::post('/kas/keluar', [KeuanganRwController::class, 'storeKeluar'])->name('keluar.store');
        });

        Route::middleware('permission:keuangan_rw.setting')->group(function () {
            Route::get('/setting', [KeuanganRwController::class, 'setting'])->name('setting');
            Route::post('/setting', [KeuanganRwController::class, 'storeSetting'])->name('setting.store');
            Route::delete('/setting/hapus-qris', [KeuanganRwController::class, 'hapusQris'])->name('setting.hapus-qris');  // ← BARU
        });

        Route::middleware('permission:keuangan_rw.export')->group(function () {
        Route::get('/kas/export', [KeuanganRwController::class, 'exportKas'])->name('kas.export');
        Route::get('/laporan/export', [KeuanganRwController::class, 'exportLaporan'])->name('laporan.export');
        });
    });

    // ============ KEUANGAN RT ============
    Route::prefix('keuangan/rt')->name('keuangan.rt.')->group(function () {
        Route::middleware('permission:keuangan_rt.view')->group(function () {
            Route::get('/', [KeuanganRtController::class, 'dashboard'])->name('dashboard');
            Route::get('/kas', [KeuanganRtController::class, 'kas'])->name('kas');
            Route::get('/belum-bayar', [KeuanganRtController::class, 'belumBayar'])->name('belum-bayar');
            Route::get('/laporan', [KeuanganRtController::class, 'laporan'])->name('laporan');
        });

        Route::middleware('permission:keuangan_rt.create')->group(function () {
            Route::get('/kas/masuk/create', [KeuanganRtController::class, 'createMasuk'])->name('masuk.create');
            Route::post('/kas/masuk', [KeuanganRtController::class, 'storeMasuk'])->name('masuk.store');
            Route::get('/kas/keluar/create', [KeuanganRtController::class, 'createKeluar'])->name('keluar.create');
            Route::post('/kas/keluar', [KeuanganRtController::class, 'storeKeluar'])->name('keluar.store');
        });

         Route::middleware('permission:keuangan_rt.export')->group(function () {
        Route::get('/kas/export', [KeuanganRtController::class, 'exportKas'])->name('kas.export');
        Route::get('/laporan/export', [KeuanganRtController::class, 'exportLaporan'])->name('laporan.export');
        });
    });

    // ============ REKAP KEUANGAN (KETUA RW) ============
    Route::prefix('keuangan/rekap')->name('keuangan.rekap.')->group(function () {
        Route::middleware('permission:keuangan_rekap.view')->group(function () {
            Route::get('/', [KeuanganRekapController::class, 'dashboard'])->name('dashboard');
            Route::get('/rt/{id}', [KeuanganRekapController::class, 'showRt'])->name('rt');
            Route::get('/laporan', [KeuanganRekapController::class, 'laporan'])->name('laporan');
        });

        Route::middleware('permission:keuangan_rekap.view')->group(function () {
        Route::get('/export', [KeuanganRekapController::class, 'exportRekap'])->name('export');
        });
    });

    // ============ KEUANGAN WARGA ============
    Route::prefix('keuangan/warga')->name('keuangan.warga.')->group(function () {
        Route::middleware('permission:tagihan.view')->group(function () {
            Route::get('/', [KeuanganWargaController::class, 'index'])->name('index');
            Route::get('/grafik', [KeuanganWargaController::class, 'grafik'])->name('grafik');
            Route::get('/tagihan/{id}', [KeuanganWargaController::class, 'show'])->name('show');
            Route::get('/kwitansi/{pembayaran}', [KeuanganWargaController::class, 'kwitansi'])->name('kwitansi');
        });

        Route::middleware('permission:tagihan.bayar')->group(function () {
            Route::post('/konfirmasi/{tagihan}', [KeuanganWargaController::class, 'konfirmasiBayar'])->name('konfirmasi');
        });
    });

    // ============ PENGATURAN PEMBAYARAN ============
    Route::prefix('pengaturan/pembayaran')->name('pengaturan.pembayaran.')->group(function () {
        Route::middleware('permission:keuangan_rw.setting')->group(function () {
            Route::get('/', [PengaturanPembayaranController::class, 'index'])->name('index');
            Route::put('/update', [PengaturanPembayaranController::class, 'update'])->name('update');
            Route::delete('/hapus-qris', [PengaturanPembayaranController::class, 'hapusQris'])->name('hapus-qris');
        });
    });
});

    // ============ MANAJEMEN TAMU ============
    Route::prefix('tamu')->name('tamu.')->group(function () {
        Route::middleware('permission:tamu.view')->group(function () {
            Route::get('/', [TamuController::class, 'index'])->name('index');
        });
        Route::middleware('permission:tamu.create')->group(function () {
            Route::get('/create', [TamuController::class, 'create'])->name('create');
            Route::post('/', [TamuController::class, 'store'])->name('store');
            
        });
        Route::middleware('permission:tamu.view')->group(function () {
            Route::get('/{tamu}', [TamuController::class, 'show'])->name('show');
        });
        Route::middleware('permission:tamu.edit')->group(function () {
            Route::get('/{tamu}/edit', [TamuController::class, 'edit'])->name('edit');
            Route::put('/{tamu}', [TamuController::class, 'update'])->name('update');
        });
        Route::middleware('permission:tamu.checkout')->group(function () {
            Route::post('/{tamu}/checkout', [TamuController::class, 'checkout'])->name('checkout');
        });
        Route::middleware('permission:tamu.delete')->group(function () {
            Route::delete('/{tamu}', [TamuController::class, 'destroy'])->name('destroy');
        });
    });


        // ============ POSYANDU (HALAMAN UTAMA) ============
    Route::get('/posyandu', [\App\Http\Controllers\PosyanduController::class, 'index'])
        ->middleware('permission:posyandu.view')
        ->name('posyandu.index');

    // ============ POSYANDU ============
    Route::prefix('posyandu')->name('posyandu.')->group(function () {

        // Jadwal
        Route::prefix('jadwal')->name('jadwal.')->group(function () {
            Route::middleware('permission:posyandu.view')->group(function () {
                Route::get('/', [PosyanduJadwalController::class, 'index'])->name('index');
            });
            Route::middleware('permission:posyandu.create')->group(function () {
                Route::get('/create', [PosyanduJadwalController::class, 'create'])->name('create');
                Route::post('/', [PosyanduJadwalController::class, 'store'])->name('store');
            });
            Route::middleware('permission:posyandu.view')->group(function () {
                Route::get('/{jadwal}', [PosyanduJadwalController::class, 'show'])->name('show');
            });
            Route::middleware('permission:posyandu.edit')->group(function () {
                Route::get('/{jadwal}/edit', [PosyanduJadwalController::class, 'edit'])->name('edit');
                Route::put('/{jadwal}', [PosyanduJadwalController::class, 'update'])->name('update');
            });
            Route::middleware('permission:posyandu.delete')->group(function () {
                Route::delete('/{jadwal}', [PosyanduJadwalController::class, 'destroy'])->name('destroy');
            });
        });
    });

    // ============ UMKM ============
    Route::prefix('umkm')->name('umkm.')->group(function () {
        Route::middleware('permission:umkm.view')->group(function () {
            Route::get('/', [UmkmController::class, 'index'])->name('index');
        });
        Route::middleware('permission:umkm.create')->group(function () {
            Route::get('/create', [UmkmController::class, 'create'])->name('create');
            Route::post('/', [UmkmController::class, 'store'])->name('store');
        });
        Route::middleware('permission:umkm.view')->group(function () {
            Route::get('/{umkm}', [UmkmController::class, 'show'])->name('show');
        });
        Route::middleware('permission:umkm.create')->group(function () {
            Route::get('/{umkm}/edit', [UmkmController::class, 'edit'])->name('edit');
            Route::put('/{umkm}', [UmkmController::class, 'update'])->name('update');
        });
        Route::middleware('permission:umkm.delete')->group(function () {
            Route::delete('/{umkm}', [UmkmController::class, 'destroy'])->name('destroy');
        });
        Route::middleware('permission:umkm.verifikasi')->group(function () {
            Route::post('/{umkm}/verifikasi', [UmkmController::class, 'verifikasi'])->name('verifikasi');
        });
    });

    // ============ UMKM PRODUK ============
    Route::prefix('umkm-produk')->name('umkm.produk.')->group(function () {
        Route::middleware('permission:umkm.view')->group(function () {
            Route::get('/', [UmkmProdukController::class, 'index'])->name('index');
        });
        Route::middleware('permission:umkm.create')->group(function () {
            Route::get('/create', [UmkmProdukController::class, 'create'])->name('create');
            Route::post('/', [UmkmProdukController::class, 'store'])->name('store');
        });
        Route::middleware('permission:umkm.view')->group(function () {
            Route::get('/{produk}', [UmkmProdukController::class, 'show'])->name('show');
        });
        Route::middleware('permission:umkm.create')->group(function () {
            Route::get('/{produk}/edit', [UmkmProdukController::class, 'edit'])->name('edit');
            Route::put('/{produk}', [UmkmProdukController::class, 'update'])->name('update');
        });
        Route::middleware('permission:umkm.delete')->group(function () {
            Route::delete('/{produk}', [UmkmProdukController::class, 'destroy'])->name('destroy');
        });
    });

        // ============ BANTUAN SOSIAL (HALAMAN UTAMA) ============
    Route::get('/bansos', [\App\Http\Controllers\BansosController::class, 'index'])
        ->middleware('permission:bansos.view')
        ->name('bansos.index');

     // ============ BANTUAN SOSIAL ============
    Route::prefix('bansos')->name('bansos.')->group(function () {

        // Program
        Route::prefix('program')->name('program.')->group(function () {
            Route::middleware('permission:bansos.view')->group(function () {
                Route::get('/', [BansosProgramController::class, 'index'])->name('index');
            });
            Route::middleware('permission:bansos.create')->group(function () {
                Route::get('/create', [BansosProgramController::class, 'create'])->name('create');
                Route::post('/', [BansosProgramController::class, 'store'])->name('store');
            });
            Route::middleware('permission:bansos.view')->group(function () {
                Route::get('/{program}', [BansosProgramController::class, 'show'])->name('show');
            });
            Route::middleware('permission:bansos.edit')->group(function () {
                Route::get('/{program}/edit', [BansosProgramController::class, 'edit'])->name('edit');
                Route::put('/{program}', [BansosProgramController::class, 'update'])->name('update');
            });
            Route::middleware('permission:bansos.delete')->group(function () {
                Route::delete('/{program}', [BansosProgramController::class, 'destroy'])->name('destroy');
            });
        });

        // Penerima
        Route::prefix('penerima')->name('penerima.')->group(function () {
            Route::middleware('permission:bansos.view')->group(function () {
                Route::get('/', [BansosPenerimaController::class, 'index'])->name('index');
            });
            Route::middleware('permission:bansos.create')->group(function () {
                Route::get('/create', [BansosPenerimaController::class, 'create'])->name('create');
                Route::post('/', [BansosPenerimaController::class, 'store'])->name('store');
            });
            Route::middleware('permission:bansos.view')->group(function () {
                Route::get('/{penerima}', [BansosPenerimaController::class, 'show'])->name('show');
            });
            Route::middleware('permission:bansos.edit')->group(function () {
                Route::get('/{penerima}/edit', [BansosPenerimaController::class, 'edit'])->name('edit');
                Route::put('/{penerima}', [BansosPenerimaController::class, 'update'])->name('update');
            });
            Route::middleware('permission:bansos.verifikasi')->group(function () {
                Route::post('/{penerima}/verifikasi', [BansosPenerimaController::class, 'verifikasi'])->name('verifikasi');
            });
            Route::middleware('permission:bansos.delete')->group(function () {
                Route::delete('/{penerima}', [BansosPenerimaController::class, 'destroy'])->name('destroy');
            });
        });

        // Penyaluran
        Route::prefix('penyaluran')->name('penyaluran.')->group(function () {
            Route::middleware('permission:bansos.view')->group(function () {
                Route::get('/', [BansosPenyaluranController::class, 'index'])->name('index');
            });
            Route::middleware('permission:bansos.salurkan')->group(function () {
                Route::get('/create', [BansosPenyaluranController::class, 'create'])->name('create');
                Route::post('/', [BansosPenyaluranController::class, 'store'])->name('store');
            });
            Route::middleware('permission:bansos.view')->group(function () {
                Route::get('/{penyaluran}', [BansosPenyaluranController::class, 'show'])->name('show');
            });
            Route::middleware('permission:bansos.salurkan')->group(function () {
                Route::get('/{penyaluran}/edit', [BansosPenyaluranController::class, 'edit'])->name('edit');
                Route::put('/{penyaluran}', [BansosPenyaluranController::class, 'update'])->name('update');
            });
            Route::middleware('permission:bansos.delete')->group(function () {
                Route::delete('/{penyaluran}', [BansosPenyaluranController::class, 'destroy'])->name('destroy');
            });
        });
    });


    // Root redirect
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

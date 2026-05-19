<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KegiatanController;
use App\Http\Controllers\PublicDashboardController;
use App\Http\Controllers\PendaftaranKegiatanController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ApiPtkController;
use App\Http\Controllers\AuthKegiatanController;


Route::get('/', [PublicDashboardController::class, 'index']);
Route::get('/dashboard', [PublicDashboardController::class, 'index'])->name('dashboard');

Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/daftar-kegiatan/{slug}', [PendaftaranKegiatanController::class, 'create'])
    ->name('kegiatan.daftar');

Route::post('/daftar-kegiatan/{slug}', [PendaftaranKegiatanController::class, 'store'])
    ->name('kegiatan.daftar.store');

// Route::get('/pendaftaran-berhasil/{kelas}', [PendaftaranKegiatanController::class, 'success'])
//     ->name('kegiatan.daftar.success');
Route::get('/{slug}/pendaftaran-berhasil/{pesertaKegiatan}', [PendaftaranKegiatanController::class, 'success'])
    ->name('kegiatan.daftar.success');

Route::get('/pendaftaran/cari-sekolah', [PendaftaranKegiatanController::class, 'searchSekolah'])
    ->name('pendaftaran.cari.sekolah');
Route::get('/pendaftaran/ptk/cek-nip', [ApiPtkController::class, 'cekNip'])->name('pendaftaran.ptk.cek-nip');
Route::get('/pendaftaran/ptk/cek-nik', [ApiPtkController::class, 'cekNik'])->name('pendaftaran.ptk.cek-nik');


// ============ ROUTE KEGIATAN (LOGIN PESERTA) ============
// Penting: Semua route kegiatan harus memiliki prefix yang jelas, misal /kegiatan/
// Agar tidak bentrok dengan route admin
Route::prefix('kegiatan')->group(function () {
    Route::get('/{slug}/login', [AuthKegiatanController::class, 'showLoginForm'])->name('kegiatan.login.form');
    Route::post('/{slug}/login', [AuthKegiatanController::class, 'login'])->name('kegiatan.login');
    Route::get('/{slug}/dashboard', [AuthKegiatanController::class, 'dashboard'])->name('kegiatan.dashboard');
    Route::post('/{slug}/logout', [AuthKegiatanController::class, 'logout'])->name('kegiatan.logout');
    Route::get('/{slug?}/kelas', [KelasController::class, 'index'])
        ->name('kegiatan.kelas.index')
        ->where('slug', '.*');
});

Route::get('/sertifikat', function () {
    return 'Halaman Sertifikat Publik';
})->name('sertifikat.index');

Route::view('/bantuan', 'bantuan')->name('bantuan.index');

Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])
        ->name('admin.dashboard');

    Route::get('/admin/kegiatan', [KegiatanController::class, 'index'])
        ->name('admin.kegiatan.index');

    Route::post('/admin/kegiatan', [KegiatanController::class, 'store'])
        ->name('admin.kegiatan.store');

    Route::post('/admin/fasilitator', [KegiatanController::class, 'storeFasilitator'])
        ->name('admin.fasilitator.store');

    Route::get('/admin/kegiatan/{kegiatan}/edit', [KegiatanController::class, 'edit'])
        ->name('admin.kegiatan.edit');

    Route::put('/admin/kegiatan/{kegiatan}', [KegiatanController::class, 'update'])
        ->name('admin.kegiatan.update');

    Route::patch('/admin/kegiatan/{kegiatan}/status', [KegiatanController::class, 'updateStatus'])
        ->name('admin.kegiatan.update.status');

    // Route untuk update token kegiatan
    Route::patch('/admin/kegiatan/{kegiatan}/token', [KegiatanController::class, 'updateToken'])
        ->name('admin.kegiatan.update.token');

    Route::post('/admin/kegiatan/{kegiatan}/moodle-injected', [KegiatanController::class, 'markMoodleInjected'])
        ->name('admin.kegiatan.moodle.injected');

    Route::delete('/admin/kegiatan/{kegiatan}', [KegiatanController::class, 'destroy'])
        ->name('admin.kegiatan.destroy');

    Route::post('/admin/courses', [DashboardController::class, 'storeCourse'])
        ->name('admin.courses.store');

    Route::get('/admin/courses', function () {
        abort_if(auth()->user()->role !== 'admin', 403);
        return 'Halaman Kelola Kelas Admin';
    })->name('admin.courses.index');

    Route::get('/admin/users', function () {
        abort_if(auth()->user()->role !== 'admin', 403);
        return 'Halaman Kelola User Admin';
    })->name('admin.users.index');

    Route::get('/admin/certificates', function () {
        abort_if(auth()->user()->role !== 'admin', 403);
        return 'Halaman Sertifikat Admin';
    })->name('admin.certificates.index');
});

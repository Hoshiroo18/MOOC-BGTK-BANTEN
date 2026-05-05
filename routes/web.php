<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KegiatanController;
use App\Http\Controllers\PublicDashboardController;
use App\Http\Controllers\PendaftaranKegiatanController;
use App\Http\Controllers\KelasController;

Route::get('/', [PublicDashboardController::class, 'index']);
Route::get('/dashboard', [PublicDashboardController::class, 'index'])->name('dashboard');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->intended('/admin/dashboard');
        }

        return redirect()->intended('/kelas');
    }

    return back()
        ->withErrors([
            'email' => 'Email atau password salah.',
        ])
        ->onlyInput('email');
})->name('login.process');

Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/dashboard');
})->name('logout');

Route::get('/daftar-kegiatan/{slug}', [PendaftaranKegiatanController::class, 'create'])
    ->name('kegiatan.daftar');

Route::post('/daftar-kegiatan/{slug}', [PendaftaranKegiatanController::class, 'store'])
    ->name('kegiatan.daftar.store');

Route::get('/pendaftaran-berhasil/{kelas}', [PendaftaranKegiatanController::class, 'success'])
    ->name('kegiatan.daftar.success');

Route::get('/kelas', [KelasController::class, 'index'])
    ->middleware('auth')
    ->name('kelas.index');

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

    Route::get('/admin/kegiatan/{kegiatan}/edit', [KegiatanController::class, 'edit'])
        ->name('admin.kegiatan.edit');

    Route::put('/admin/kegiatan/{kegiatan}', [KegiatanController::class, 'update'])
        ->name('admin.kegiatan.update');

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
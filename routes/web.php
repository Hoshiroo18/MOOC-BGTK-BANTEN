<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KegiatanController;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

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

        return redirect()->intended('/admin/dashboard');
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

    return redirect('/login');
})->name('logout');

Route::get('/admin/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('admin.dashboard');

Route::post('/admin/courses', [DashboardController::class, 'storeCourse'])
    ->middleware('auth')
    ->name('admin.courses.store');
Route::get('/admin/courses', function () {
    return 'Halaman Kelola Kelas Admin';
})->middleware('auth')->name('admin.courses.index');

Route::get('/admin/users', function () {
    return 'Halaman Kelola User Admin';
})->middleware('auth')->name('admin.users.index');

Route::get('/admin/certificates', function () {
    return 'Halaman Sertifikat Admin';
})->middleware('auth')->name('admin.certificates.index');

Route::get('/kelas', function () {
    return 'Halaman Kelas Publik';
})->name('kelas.index');

Route::get('/sertifikat', function () {
    return 'Halaman Sertifikat Publik';
})->name('sertifikat.index');

Route::get('/bantuan', function () {
    return 'Halaman Bantuan Publik';
})->name('bantuan.index');

Route::get('/admin/kegiatan', [KegiatanController::class, 'index'])
    ->middleware('auth')
    ->name('admin.kegiatan.index');

Route::post('/admin/kegiatan', [KegiatanController::class, 'store'])
    ->middleware('auth')
    ->name('admin.kegiatan.store');

Route::get('/daftar-kegiatan/{slug}', function ($slug) {
    return 'Halaman pendaftaran untuk kegiatan: ' . $slug;
})->name('kegiatan.daftar');

Route::delete('/admin/kegiatan/{kegiatan}', [KegiatanController::class, 'destroy'])
    ->middleware('auth')
    ->name('admin.kegiatan.destroy');
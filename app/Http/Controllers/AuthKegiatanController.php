<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Peserta;
use App\Models\PesertaKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthKegiatanController extends Controller
{
    // Halaman login per kegiatan
    public function showLoginForm(string $slug)
    {
        $kegiatan = Kegiatan::where('slug', $slug)->firstOrFail();

        // Cek status kegiatan aktif atau tidak
        if (($kegiatan->status_url ?? 'active') === 'inactive') {
            abort(403, 'Maaf, kegiatan ini sedang tidak tersedia.');
        }

        // Menggunakan view login/index.blade.php
        return view('login.index', compact('kegiatan'));
    }

    // Proses login per kegiatan
    public function login(Request $request, string $slug)
    {
        $kegiatan = Kegiatan::where('slug', $slug)->firstOrFail();

        // Validasi input
        $request->validate([
            'nip' => 'required|string|max:18',
            'token' => 'required|string|max:10',
        ]);

        // Cek token kegiatan
        if ($kegiatan->token_kegiatan !== $request->token) {
            return back()->withErrors(['token' => 'Token kegiatan salah.'])->withInput();
        }

        // Cari peserta berdasarkan NIP
        $peserta = Peserta::where('nip', $request->nip)->first();

        if (!$peserta) {
            return back()->withErrors(['nip' => 'NIP tidak terdaftar.'])->withInput();
        }

        // Cek apakah peserta terdaftar di kegiatan ini
        $pesertaKegiatan = PesertaKegiatan::where('peserta_id', $peserta->peserta_id)
            ->where('kegiatan_id', $kegiatan->kegiatan_id)
            ->first();

        if (!$pesertaKegiatan) {
            return back()->withErrors(['nip' => 'Anda tidak terdaftar di kegiatan ini.'])->withInput();
        }

        // Login manual menggunakan session
        session([
            'auth_peserta' => [
                'peserta_id' => $peserta->peserta_id,
                'nama' => $peserta->nama,
                'nip' => $peserta->nip,
                'email' => $peserta->email,
                'kegiatan_id' => $kegiatan->kegiatan_id,
                'kegiatan_slug' => $kegiatan->slug,
                'kegiatan_nama' => $kegiatan->nama_kegiatan,
                'login_at' => now(),
            ]
        ]);

        return redirect()->route('kegiatan.dashboard', $kegiatan->slug)
            ->with('success', 'Selamat datang, ' . $peserta->nama . '!');
    }

    // Dashboard peserta per kegiatan
    public function dashboard(string $slug)
    {
        // Cek session login
        if (!session()->has('auth_peserta')) {
            return redirect()->route('kegiatan.login.form', $slug);
        }

        $auth = session('auth_peserta');

        // Validasi kegiatan sesuai slug
        if ($auth['kegiatan_slug'] !== $slug) {
            session()->forget('auth_peserta');
            return redirect()->route('kegiatan.login.form', $slug);
        }

        $kegiatan = Kegiatan::where('slug', $slug)->firstOrFail();
        // Cek status kegiatan aktif atau tidak
        if (($kegiatan->status_url ?? 'active') === 'inactive') {
            abort(403, 'Maaf, kegiatan ini sedang tidak tersedia.');
        }

        $peserta = Peserta::find($auth['peserta_id']);

        return view('beranda.dashboard', compact('kegiatan', 'peserta'));
    }

    // Logout
    public function logout(Request $request)
    {
        // Ambil slug kegiatan dari session sebelum dihapus
        $kegiatanSlug = session('auth_peserta.kegiatan_slug');

        // Hapus session
        session()->forget('auth_peserta');

        // Redirect ke halaman login kegiatan tersebut
        if ($kegiatanSlug) {
            return redirect()->route('kegiatan.login.form', $kegiatanSlug)
                ->with('success', 'Anda telah logout.');
        }

        // Fallback jika tidak ada slug
        return redirect('/')->with('success', 'Anda telah logout.');
    }
}

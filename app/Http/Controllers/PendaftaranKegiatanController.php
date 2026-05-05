<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PendaftaranKegiatanController extends Controller
{
    public function create(string $slug)
    {
        $kegiatan = Kegiatan::where('slug', $slug)->firstOrFail();

        return view('pendaftaran.create', compact('kegiatan'));
    }

    public function store(Request $request, string $slug)
    {
        $kegiatan = Kegiatan::where('slug', $slug)->firstOrFail();

        if (!$kegiatan->perlu_pendaftaran) {
            return redirect()
                ->route('dashboard')
                ->with('success', 'Kegiatan ini tidak membutuhkan pendaftaran.');
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'nik' => 'required|string|max:50',
            'asal_instansi' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'kabupaten_kota' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
        ]);

        $existing = Kelas::where('email', $validated['email'])
            ->where('kegiatan_id', $kegiatan->id)
            ->first();

        if ($existing) {
            return redirect()
                ->route('kegiatan.daftar.success', $existing->id)
                ->with('success', 'Kamu sudah pernah mendaftar kegiatan ini.');
        }

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            $user = User::create([
                'name' => $validated['nama'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['nik']),
                'role' => 'peserta',
            ]);
        }

        $status = 'disetujui';

        if ($kegiatan->jenis_kegiatan === 'pelatihan' && $kegiatan->jenis_pelatihan === 'terbimbing') {
            $status = 'menunggu';
        }

        $kelas = Kelas::create([
            'user_id' => $user->id,
            'kegiatan_id' => $kegiatan->id,
            'nama' => $validated['nama'],
            'nip' => $validated['nip'] ?? null,
            'nik' => $validated['nik'],
            'asal_instansi' => $validated['asal_instansi'],
            'email' => $validated['email'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'kabupaten_kota' => $validated['kabupaten_kota'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'status_pendaftaran' => $status,
        ]);

        return redirect()
            ->route('kegiatan.daftar.success', $kelas->id)
            ->with('success', 'Pendaftaran berhasil dikirim.');
    }

    public function success(Kelas $kelas)
    {
        $kelas->load('kegiatan');

        return view('pendaftaran.success', compact('kelas'));
    }
}
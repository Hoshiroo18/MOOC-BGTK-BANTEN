<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Peserta;
use App\Models\Kegiatan;
use App\Models\PesertaKegiatan;
use Carbon\Carbon;

class KelasController extends Controller
{
    public function index()
    {
        $kegiatanUser = session('auth_peserta');
        $user = auth()->user();

        if (!empty($kegiatanUser)) {
            return $this->indexKegiatan($kegiatanUser);
        }

        if ($user) {
            return $this->indexRegularUser($user);
        }

        return redirect()->route('login')->with('message', 'Silakan login terlebih dahulu.');
    }

    private function indexKegiatan($kegiatanUser)
    {
        $pesertaId  = $kegiatanUser['peserta_id']  ?? null;
        $kegiatanId = $kegiatanUser['kegiatan_id'] ?? null;
        $slug       = $kegiatanUser['kegiatan_slug'] ?? null;

        if (!$pesertaId || !$kegiatanId || !$slug) {
            return redirect()->route('kegiatan.dashboard', $slug)
                ->with('error', 'Data kegiatan tidak lengkap.');
        }

        $kegiatan = Kegiatan::where('slug', $slug)->firstOrFail();
        if (($kegiatan->status_url ?? 'active') === 'inactive') {
            abort(403, 'Maaf, kegiatan ini sedang tidak tersedia.');
        }

        $peserta = Peserta::find($pesertaId);
        if (!$peserta) {
            return redirect()->route('kegiatan.dashboard', $slug)
                ->with('error', 'Data peserta tidak ditemukan.');
        }

        // Ambil SEMUA kegiatan peserta, bukan hanya yang login saat ini
        $semuaPesertaKegiatan = PesertaKegiatan::where('peserta_id', $pesertaId)
            ->with('kegiatan.tipeKegiatan', 'kegiatan.jenisKegiatan', 'kegiatan.moda')
            ->get();

        if ($semuaPesertaKegiatan->isEmpty()) {
            return redirect()->route('kegiatan.dashboard', $slug)
                ->with('error', 'Data registrasi kegiatan tidak ditemukan.');
        }

        $kelasAktif   = collect();
        $riwayatKelas = collect();

        foreach ($semuaPesertaKegiatan as $pesertaKegiatan) {
            $kegiatan = $pesertaKegiatan->kegiatan;
            if (!$kegiatan) continue;

            $now       = Carbon::now();
            $startDate = $kegiatan->start_date ? Carbon::parse($kegiatan->start_date) : null;
            $endDate   = $kegiatan->end_date   ? Carbon::parse($kegiatan->end_date)   : null;

            $isAktif = true;

            if ($endDate && $endDate->lessThan($now)) {
                $isAktif = false;
            } elseif ($startDate && $startDate->greaterThan($now)) {
                $isAktif = true;
            } elseif ($startDate && $endDate && $startDate->lessThanOrEqualTo($now) && $endDate->greaterThanOrEqualTo($now)) {
                $isAktif = true;
            } else {
                $isAktif = ($kegiatan->status_url ?? 'active') === 'active';
            }

            if ($isAktif) {
                $kelasAktif->push($pesertaKegiatan);
            } else {
                $riwayatKelas->push($pesertaKegiatan);
            }
        }

        $totalKelas   = $semuaPesertaKegiatan->count();
        $totalAktif   = $kelasAktif->count();
        $totalRiwayat = $riwayatKelas->count();

        return view('kelas.index', compact(
            'peserta',
            'kelasAktif',
            'riwayatKelas',
            'totalKelas',
            'totalAktif',
            'totalRiwayat',
            'kegiatanUser'
        ));
    }

    private function indexRegularUser($user)
    {
        $kelas = Kelas::with('kegiatan.tipeKegiatan', 'kegiatan.jenisKegiatan', 'kegiatan.moda')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $kelasAktif = collect();
        $riwayatKelas = collect();

        foreach ($kelas as $item) {
            $kegiatan = $item->kegiatan;

            if (!$kegiatan) {
                $riwayatKelas->push($item);
                continue;
            }

            $now = Carbon::now();
            $startDate = $kegiatan->start_date ? Carbon::parse($kegiatan->start_date) : null;
            $endDate = $kegiatan->end_date ? Carbon::parse($kegiatan->end_date) : null;

            $isAktif = true;

            if ($endDate && $endDate->lessThan($now)) {
                // Kegiatan sudah selesai
                $isAktif = false;
            } elseif ($startDate && $startDate->greaterThan($now)) {
                // Kegiatan akan datang
                $isAktif = true;
            } elseif ($startDate && $endDate && $startDate->lessThanOrEqualTo($now) && $endDate->greaterThanOrEqualTo($now)) {
                // Kegiatan sedang berlangsung
                $isAktif = true;
            } else {
                // Fallback ke status_url
                $statusUrl = $kegiatan->status_url ?? 'active';
                $isAktif = ($statusUrl === 'active');
            }

            if ($isAktif) {
                $kelasAktif->push($item);
            } else {
                $riwayatKelas->push($item);
            }
        }

        $totalKelas = $kelas->count();
        $totalAktif = $kelasAktif->count();
        $totalRiwayat = $riwayatKelas->count();

        return view('kelas.index', compact(
            'user',
            'kelasAktif',
            'riwayatKelas',
            'totalKelas',
            'totalAktif',
            'totalRiwayat'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Carbon\Carbon;

class KelasController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $kelas = Kelas::with('kegiatan')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $kelasAktif = $kelas->filter(function ($item) {
            if (!$item->kegiatan || !$item->kegiatan->waktu_pelaksanaan) {
                return true;
            }

            return Carbon::parse($item->kegiatan->waktu_pelaksanaan)->greaterThanOrEqualTo(now());
        });

        $riwayatKelas = $kelas->filter(function ($item) {
            if (!$item->kegiatan || !$item->kegiatan->waktu_pelaksanaan) {
                return false;
            }

            return Carbon::parse($item->kegiatan->waktu_pelaksanaan)->lessThan(now());
        });

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
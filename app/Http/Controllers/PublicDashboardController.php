<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\PesertaKegiatan;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PublicDashboardController extends Controller
{
    public function index()
    {
        $kegiatan = Kegiatan::with([
            'tipeKegiatan',
            'jenisKegiatan',
            'moda',
            'fasilitators',
        ])->latest()->get();

        $totalKegiatan   = $kegiatan->count();
        $totalWebinar    = $kegiatan->filter(fn($k) => optional($k->tipeKegiatan)->nama_kegiatan === 'Webinar')->count();
        $totalPelatihan  = $kegiatan->filter(fn($k) => optional($k->tipeKegiatan)->nama_kegiatan === 'Seminar')->count();
        $totalKonsultasi = $kegiatan->filter(fn($k) => optional($k->tipeKegiatan)->nama_kegiatan === 'Konsultasi')->count();

        $kegiatanCards = $kegiatan->values()->map(function ($item) {

            $jenisRaw = strtolower(optional($item->tipeKegiatan)->nama_kegiatan ?? 'kegiatan');
            $modaRaw  = strtolower(optional($item->moda)->jenis_moda ?? '-');

            $jenisKegiatan = optional($item->jenisKegiatan)->jenis_kegiatan ?? '';

            $fasilNames = $item->fasilitators->pluck('nama')->filter()->join(', ');

            // $waktuText = '-';
            // if (!empty($item->waktu_pelaksanaan)) {
            //     $waktuText = Carbon::parse($item->waktu_pelaksanaan)->format('d M Y, H:i');
            // }
            $waktuText = '-';
            if (!empty($item->start_date) || !empty($item->end_date)) {
                $start = $item->start_date ? Carbon::parse($item->start_date)->format('d M Y') : '-';
                $end   = $item->end_date   ? Carbon::parse($item->end_date)->format('d M Y')   : '-';
                $waktuText = $start . ' s/d ' . $end;
            }

            $flayerUrl = !empty($item->flayer)
                ? asset('storage/' . $item->flayer)
                : asset('images/baduy.jpg');



            return [
                'id'                       => $item->kegiatan_id,
                'title'                    => $item->nama_kegiatan ?? 'Kegiatan MOOC',
                'slug'                     => $item->slug ?? '',
                'jenis_raw'                => $jenisRaw,
                'jenis'                    => ucfirst($jenisRaw),
                'moda_raw'                 => $modaRaw,
                'moda'                     => ucfirst($modaRaw),
                'deskripsi'                => strip_tags($item->deskripsi ?? ''),
                'deskripsi_short'          => Str::limit(strip_tags($item->deskripsi ?? ''), 125),
                'fasil'                    => $fasilNames ?: '-',
                'kuota'                    => $item->kuota ?? '-',
                'waktu'                    => $waktuText,
                'flayer'                   => $flayerUrl,
                'link_pendaftaran'         => $item->link_pendaftaran ?? '',
                'link_zoom'                => $item->link_zoom ?? '',
                'moodle_link'              => $item->link_lms ?? '',
                'course_name'              => 'Course Moodle',
                'jenis_kegiatan'           => $jenisKegiatan,
                'is_registration_required' => (bool) ($item->is_registration_required ?? false),
            ];
        });

        $kegiatanUser = session('auth_peserta');

        // Ambil semua kegiatan_id yang didaftari peserta ini
        $pesertaKegiatanIds = [];
        if (!empty($kegiatanUser['peserta_id'])) {
            $pesertaKegiatanIds = PesertaKegiatan::where('peserta_id', $kegiatanUser['peserta_id'])
                ->pluck('status', 'kegiatan_id') // ← key=kegiatan_id, value=status (semua status)
                ->toArray();
        }

        return view('dashboard', compact(
            'kegiatanCards',
            'totalKegiatan',
            'totalWebinar',
            'totalPelatihan',
            'totalKonsultasi',
            'kegiatanUser',
            'pesertaKegiatanIds'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use App\Models\Sekolah;
use App\Models\Kota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ApiPtkController extends Controller
{
    // =========================================================================
    // LOGIN KE API DAPODIK
    // =========================================================================
    private function loginToApi(): array
    {
        $baseUrl  = config('api.base_url');
        $email    = config('api.email');
        $password = config('api.password');

        try {
            $response = Http::timeout(30)->post($baseUrl . '/login', [
                'email'    => $email,
                'password' => $password,
            ]);

            if ($response->status() !== 200) {
                Log::error('ApiPtk: Gagal login', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return ['success' => false, 'message' => 'Gagal login ke API Dapodik'];
            }

            $token = $response->json('token');
            if (!$token) {
                return ['success' => false, 'message' => 'Token tidak ditemukan di response'];
            }

            return ['success' => true, 'token' => $token];
        } catch (\Exception $e) {
            Log::error('ApiPtk: Exception saat login', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Gagal terhubung ke API: ' . $e->getMessage()];
        }
    }

    // =========================================================================
    // FETCH PTK DARI API BERDASARKAN NIP ATAU NIK
    // =========================================================================
    private function fetchPtkFromApi(string $token, string $searchKey): ?array
    {
        $baseUrl = config('api.base_url');

        try {
            $response = Http::withToken($token)
                ->timeout(30)
                ->get($baseUrl . '/ptk/' . urlencode($searchKey));

            Log::info('ApiPtk: fetchPtk', [
                'searchKey' => $searchKey,
                'status'    => $response->status(),
            ]);

            if ($response->status() !== 200) return null;

            $data = $response->json();

            if (empty($data) || (!isset($data['nama']) && !isset($data['nip']) && !isset($data['nik']))) {
                return null;
            }

            return $data;
        } catch (\Exception $e) {
            Log::warning('ApiPtk: fetchPtk error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    // =========================================================================
    // FETCH SEKOLAH DARI API
    // =========================================================================
    private function fetchSekolahFromApi(string $token, string $searchKey): ?array
    {
        $baseUrl = config('api.base_url');

        try {
            $response = Http::withToken($token)
                ->timeout(30)
                ->get($baseUrl . '/sekolah/' . urlencode($searchKey));

            if ($response->status() !== 200) return null;

            $data = $response->json();
            return (is_array($data) && !empty($data)) ? $data : null;
        } catch (\Exception $e) {
            Log::warning('ApiPtk: fetchSekolah error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    // =========================================================================
    // RESOLVE KOTA_ID DARI NAMA KOTA
    // =========================================================================
    private function resolveKotaIdFromName(?string $namaKota): ?int
    {
        if (empty($namaKota)) return null;

        $kota = Kota::where('nama_kota', 'LIKE', '%' . $namaKota . '%')->first();

        if (!$kota) {
            $cleanName = trim(preg_replace('/^(KAB|KABUPATEN|KOTA)\s+/i', '', $namaKota));
            $kota = Kota::where('nama_kota', 'LIKE', '%' . $cleanName . '%')->first();
        }

        return $kota ? $kota->kota_id : null;
    }

    // =========================================================================
    // RESOLVE SEKOLAH DARI API (cari berdasarkan keyword)
    // =========================================================================
    private function resolveSekolahDariApi(string $token, string $keyword): ?array
    {
        if (empty(trim($keyword))) return null;

        $data = $this->fetchSekolahFromApi($token, $keyword);
        if (!$data) return null;

        $list = (is_array($data) && isset($data[0])) ? $data : [$data];

        foreach ($list as $item) {
            $nama = is_array($item)
                ? ($item['nama_sekolah'] ?? $item['nama'] ?? null)
                : ($item->nama_sekolah  ?? $item->nama   ?? null);

            if (!empty($nama)) {
                $npsn = is_array($item) ? ($item['npsn'] ?? '') : ($item->npsn ?? '');
                $sekolahLokal = Sekolah::where('npsn', $npsn)
                    ->orWhere('nama_sekolah', 'LIKE', '%' . $nama . '%')
                    ->first();

                if ($sekolahLokal) {
                    return [
                        'sekolah_id'   => $sekolahLokal->sekolah_id,
                        'npsn'         => $sekolahLokal->npsn,
                        'nama_sekolah' => $sekolahLokal->nama_sekolah,
                        'kab_kota'     => $sekolahLokal->kab_kota,
                        'jenjang'      => $sekolahLokal->jenjang,
                        'alamat'       => $sekolahLokal->alamat,
                    ];
                }

                return [
                    'sekolah_id'   => is_array($item) ? ($item['sekolah_id']  ?? null) : ($item->sekolah_id  ?? null),
                    'npsn'         => $npsn,
                    'nama_sekolah' => $nama,
                    'kab_kota'     => is_array($item) ? ($item['kab_kota'] ?? $item['kota'] ?? null) : ($item->kab_kota ?? $item->kota ?? null),
                    'jenjang'      => is_array($item) ? ($item['jenjang']      ?? null) : ($item->jenjang     ?? null),
                    'alamat'       => is_array($item) ? ($item['alamat']       ?? null) : ($item->alamat      ?? null),
                ];
            }
        }

        return null;
    }

    // =========================================================================
    // RESOLVE SEKOLAH DARI DATA PTK API
    // =========================================================================
    private function resolveSekolahFromPtkData(string $token, array $ptkData): ?array
    {
        Log::info('ApiPtk: resolveSekolahFromPtkData - mulai', [
            'sekolah_id' => $ptkData['sekolah_id'] ?? null,
            'npsn' => $ptkData['npsn'] ?? null,
            'nama_sekolah' => $ptkData['nama_sekolah'] ?? null,
            'instansi' => $ptkData['instansi'] ?? null,
        ]);

        $idCandidates = array_filter([
            isset($ptkData['sekolah_id']) && $ptkData['sekolah_id'] !== '' ? (string)$ptkData['sekolah_id'] : null,
            isset($ptkData['npsn']) && $ptkData['npsn'] !== '' ? (string)$ptkData['npsn'] : null,
        ]);

        $nameCandidates = array_filter([
            $ptkData['nama_sekolah']      ?? null,
            $ptkData['instansi']          ?? null,
            $ptkData['nama_instansi']     ?? null,
            $ptkData['satuan_pendidikan'] ?? null,
            $ptkData['unit_kerja']        ?? null,
            $ptkData['sekolah']           ?? null,
            $ptkData['asal_sekolah']      ?? null,
        ]);

        // Cari di database lokal dulu berdasarkan ID/NPSN
        foreach ($idCandidates as $id) {
            $sekolahLokal = Sekolah::where('npsn', $id)
                ->orWhere('sekolah_id', $id)
                ->first();
            if ($sekolahLokal) {
                return [
                    'sekolah_id'   => $sekolahLokal->sekolah_id,
                    'npsn'         => $sekolahLokal->npsn,
                    'nama_sekolah' => $sekolahLokal->nama_sekolah,
                    'kab_kota'     => $sekolahLokal->kab_kota,
                    'jenjang'      => $sekolahLokal->jenjang,
                    'alamat'       => $sekolahLokal->alamat,
                ];
            }
        }

        // Cari di database lokal berdasarkan nama
        foreach ($nameCandidates as $nama) {
            $nama = trim((string) $nama);
            if (empty($nama)) continue;
            $sekolahLokal = Sekolah::where('nama_sekolah', 'LIKE', '%' . $nama . '%')->first();
            if ($sekolahLokal) {
                return [
                    'sekolah_id'   => $sekolahLokal->sekolah_id,
                    'npsn'         => $sekolahLokal->npsn,
                    'nama_sekolah' => $sekolahLokal->nama_sekolah,
                    'kab_kota'     => $sekolahLokal->kab_kota,
                    'jenjang'      => $sekolahLokal->jenjang,
                    'alamat'       => $sekolahLokal->alamat,
                ];
            }
        }

        // Cari ke API Dapodik
        foreach ($idCandidates as $id) {
            $result = $this->resolveSekolahDariApi($token, $id);
            if ($result) return $result;
        }

        foreach ($nameCandidates as $nama) {
            $nama = trim((string) $nama);
            if (empty($nama)) continue;
            $result = $this->resolveSekolahDariApi($token, $nama);
            if ($result) return $result;
        }

        return null;
    }

    // =========================================================================
    // FORMAT DATA DARI API DAPODIK UNTUK AUTOFILL
    // =========================================================================
    private function formatForForm(array $data, ?array $sekolahData = null): array
    {
        $tanggalLahir = null;
        if (!empty($data['tanggal_lahir'])) {
            try {
                $tanggalLahir = Carbon::parse($data['tanggal_lahir'])->format('Y-m-d');
            } catch (\Exception $e) {
                $tanggalLahir = $data['tanggal_lahir'];
            }
        }

        $jk = strtoupper(trim($data['jenis_kelamin'] ?? ''));
        $jkFormatted = '';
        if ($jk === 'L' || str_starts_with($jk, 'L')) {
            $jkFormatted = 'Laki-laki';
        } elseif ($jk === 'P' || str_starts_with($jk, 'P')) {
            $jkFormatted = 'Perempuan';
        }

        $kabKota = $data['kab_kota'] ?? $data['kota'] ?? $data['tempat_lahir'] ?? null;
        $kotaId = $this->resolveKotaIdFromName($kabKota);

        if ($sekolahData && empty($kotaId) && !empty($sekolahData['kab_kota'])) {
            $kotaId = $this->resolveKotaIdFromName($sekolahData['kab_kota']);
        }

        $instansiNama = null;
        if ($sekolahData && !empty($sekolahData['nama_sekolah'])) {
            $instansiNama = $sekolahData['nama_sekolah'];
        } elseif (!empty($data['instansi'])) {
            $instansiNama = $data['instansi'];
        } elseif (!empty($data['nama_sekolah'])) {
            $instansiNama = $data['nama_sekolah'];
        }

        return [
            'nama'          => $data['nama'] ?? $data['nama_ptk'] ?? $data['nama_lengkap'] ?? null,
            'nip'           => $data['nip'] ?? null,
            'nik'           => $data['nik'] ?? null,
            'email'         => $data['email'] ?? null,
            'jenis_kelamin' => $jkFormatted,
            'tanggal_lahir' => $tanggalLahir,
            'tempat_lahir'  => $data['tempat_lahir'] ?? $data['tmp_lahir'] ?? null,
            'kota_id'       => $kotaId,
            'kota_nama'     => $kabKota,
            'instansi_nama' => $instansiNama,
        ];
    }

    // =========================================================================
    // FORMAT DATA PESERTA LOKAL UNTUK AUTOFILL (DARI TABEL PESERTA)
    // =========================================================================
    private function formatFromPesertaLokal($peserta): array
    {
        $tanggalLahir = null;
        if ($peserta->tanggal_lahir) {
            try {
                $tanggalLahir = Carbon::parse($peserta->tanggal_lahir)->format('Y-m-d');
            } catch (\Exception $e) {
                $tanggalLahir = $peserta->tanggal_lahir;
            }
        }

        // Ambil nama instansi dari tabel peserta (field 'Instansi')
        $instansiNama = $peserta->Instansi ?? null;

        // Jika tidak ada Instansi, coba ambil dari sekolah
        if (empty($instansiNama) && $peserta->sekolah_id) {
            $sekolah = Sekolah::find($peserta->sekolah_id);
            if ($sekolah) {
                $instansiNama = $sekolah->nama_sekolah;
            }
        }

        Log::info('ApiPtk: formatFromPesertaLokal', [
            'peserta_id' => $peserta->peserta_id,
            'nama' => $peserta->nama,
            'instansi_nama' => $instansiNama,
            'sekolah_id' => $peserta->sekolah_id,
        ]);

        return [
            'nama'          => $peserta->nama ?? null,
            'nip'           => $peserta->nip ?? null,
            'nik'           => $peserta->nik ?? null,
            'email'         => $peserta->email ?? null,
            'jenis_kelamin' => $peserta->jenis_kelamin ?? null,
            'tanggal_lahir' => $tanggalLahir,
            'tempat_lahir'  => null,
            'kota_id'       => $peserta->kota_id ?? null,
            'instansi_nama' => $instansiNama,
        ];
    }

    // =========================================================================
    // ENDPOINT: CEK NIP
    // =========================================================================
    public function cekNip(Request $request)
    {
        $nip = trim($request->query('nip', ''));

        if (empty($nip)) {
            return response()->json(['success' => false, 'message' => 'NIP tidak boleh kosong.']);
        }

        Log::info('ApiPtk: cekNip', ['nip' => $nip]);

        // STEP 1: Cek tabel PESERTA lokal (bukan PTK)
        $pesertaLokal = Peserta::where('nip', $nip)->first();

        if ($pesertaLokal) {
            Log::info('ApiPtk: NIP ditemukan di tabel PESERTA lokal');

            $sekolahLokal = null;
            if ($pesertaLokal->sekolah_id) {
                $sekolah = Sekolah::find($pesertaLokal->sekolah_id);
                if ($sekolah) {
                    $sekolahLokal = [
                        'sekolah_id'   => $sekolah->sekolah_id,
                        'npsn'         => $sekolah->npsn,
                        'nama_sekolah' => $sekolah->nama_sekolah,
                        'kab_kota'     => $sekolah->kab_kota,
                        'jenjang'      => $sekolah->jenjang,
                        'alamat'       => $sekolah->alamat,
                    ];
                }
            }

            $formattedData = $this->formatFromPesertaLokal($pesertaLokal);

            return response()->json([
                'success' => true,
                'source'  => 'peserta_lokal',
                'data'    => $formattedData,
                'sekolah' => $sekolahLokal,
                'message' => 'Data ditemukan dari riwayat pendaftaran.',
            ]);
        }

        // STEP 2: (HAPUS) Tidak ada tabel PTK lokal, langsung ke API Dapodik

        // STEP 3: Cari ke API Dapodik
        $loginResult = $this->loginToApi();
        if (!$loginResult['success']) {
            return response()->json(['success' => false, 'message' => $loginResult['message']]);
        }
        $token = $loginResult['token'];

        $apiData = $this->fetchPtkFromApi($token, $nip);
        if (!$apiData) {
            return response()->json([
                'success' => false,
                'message' => 'Data NIP tidak ditemukan. Silakan isi form secara manual.',
            ]);
        }

        $sekolahData = $this->resolveSekolahFromPtkData($token, $apiData);

        return response()->json([
            'success' => true,
            'source'  => 'dapodik',
            'data'    => $this->formatForForm($apiData, $sekolahData),
            'sekolah' => $sekolahData,
            'message' => 'Data ditemukan dari API Dapodik.',
        ]);
    }

    // =========================================================================
    // ENDPOINT: CEK NIK
    // =========================================================================
    public function cekNik(Request $request)
    {
        $nik = trim($request->query('nik', ''));

        if (empty($nik)) {
            return response()->json(['success' => false, 'message' => 'NIK tidak boleh kosong.']);
        }

        if (!preg_match('/^\d{16}$/', $nik)) {
            return response()->json(['success' => false, 'message' => 'NIK harus 16 digit angka.']);
        }

        Log::info('ApiPtk: cekNik', ['nik' => $nik]);

        // STEP 1: Cek tabel PESERTA lokal
        $pesertaLokal = Peserta::where('nik', $nik)->first();

        if ($pesertaLokal) {
            Log::info('ApiPtk: NIK ditemukan di tabel PESERTA lokal');

            $sekolahLokal = null;
            if ($pesertaLokal->sekolah_id) {
                $sekolah = Sekolah::find($pesertaLokal->sekolah_id);
                if ($sekolah) {
                    $sekolahLokal = [
                        'sekolah_id'   => $sekolah->sekolah_id,
                        'npsn'         => $sekolah->npsn,
                        'nama_sekolah' => $sekolah->nama_sekolah,
                        'kab_kota'     => $sekolah->kab_kota,
                        'jenjang'      => $sekolah->jenjang,
                        'alamat'       => $sekolah->alamat,
                    ];
                }
            }

            $formattedData = $this->formatFromPesertaLokal($pesertaLokal);

            return response()->json([
                'success' => true,
                'source'  => 'peserta_lokal',
                'data'    => $formattedData,
                'sekolah' => $sekolahLokal,
                'message' => 'Data ditemukan dari riwayat pendaftaran.',
            ]);
        }

        // STEP 2: (HAPUS) Tidak ada tabel PTK lokal

        // STEP 3: Cari ke API Dapodik
        $loginResult = $this->loginToApi();
        if (!$loginResult['success']) {
            return response()->json(['success' => false, 'message' => $loginResult['message']]);
        }
        $token = $loginResult['token'];

        $apiData = $this->fetchPtkFromApi($token, $nik);
        if (!$apiData) {
            return response()->json([
                'success' => false,
                'message' => 'Data NIK tidak ditemukan. Silakan isi form secara manual.',
            ]);
        }

        $sekolahData = $this->resolveSekolahFromPtkData($token, $apiData);

        return response()->json([
            'success' => true,
            'source'  => 'dapodik',
            'data'    => $this->formatForForm($apiData, $sekolahData),
            'sekolah' => $sekolahData,
            'message' => 'Data ditemukan dari API Dapodik.',
        ]);
    }
}

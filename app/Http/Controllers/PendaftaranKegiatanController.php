<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Kota;
use App\Models\Peserta;
use App\Models\PesertaKegiatan;
use App\Models\Sekolah;
use Hashids\Hashids;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PendaftaranKegiatanController extends Controller
{

    // Encode ID ke Hash
    private function encodeId($id)
    {
        $hashids = new Hashids('mooc-bgtk-banten-secret', 10);
        return $hashids->encode($id);
    }

    // Decode Hash ke ID
    private function decodeId($hash)
    {
        $hashids = new Hashids('mooc-bgtk-banten-secret', 10);
        $decoded = $hashids->decode($hash);
        return $decoded[0] ?? null;
    }

    // ─── Halaman Form Pendaftaran ────────────────────────────────────────────────

    public function create(string $slug)
    {
        $kegiatan = Kegiatan::with(['tipeKegiatan', 'jenisKegiatan', 'moda', 'fasilitators'])
            ->where('slug', $slug)
            ->firstOrFail();

        // CEK STATUS URL - Jika inactive, tampilkan halaman tidak tersedia
        if (($kegiatan->status_url ?? 'active') === 'inactive') {
            abort(403, 'Maaf, halaman pendaftaran kegiatan ini sedang tidak tersedia.');
        }

        $kotaList = Kota::orderBy('nama_kota')->get();

        return view('pendaftaran.create', compact('kegiatan', 'kotaList'));
    }

    // ─── Simpan Pendaftaran ──────────────────────────────────────────────────────

    public function store(Request $request, string $slug)
    {
        $kegiatan = Kegiatan::with(['tipeKegiatan', 'jenisKegiatan', 'moda', 'fasilitators'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Jika tidak perlu pendaftaran, redirect langsung
        if (!$kegiatan->is_registration_required) {
            return redirect()
                ->route('dashboard')
                ->with('success', 'Kegiatan ini tidak membutuhkan pendaftaran.');
        }

        $validated = $request->validate([
            'nama'          => 'required|string|max:255',
            'nip'           => 'nullable|string|max:18',
            'nik'           => 'required|string|max:16',
            'email'         => 'required|email|max:100',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'required|date',
            'kota_id'       => 'nullable|integer|exists:kota,kota_id',
            'sekolah_id'    => 'nullable|integer|exists:sekolah,sekolah_id',
            'Instansi'      => 'nullable|string|max:255',
        ]);

        // Wajib ada salah satu: sekolah dari daftar atau instansi manual
        if (empty($validated['sekolah_id']) && empty($validated['Instansi'])) {
            return back()
                ->withInput()
                ->withErrors(['Instansi' => 'Pilih sekolah dari daftar atau isi nama instansi secara manual.']);
        }

        // ── Tentukan nilai Instansi dan sekolah_id ───────────────────────────
        $instansiValue  = null;
        $sekolahIdValue = null;

        if (!empty($validated['sekolah_id'])) {
            $sekolah = Sekolah::find($validated['sekolah_id']);
            if ($sekolah) {
                $instansiValue  = $sekolah->nama_sekolah;
                $sekolahIdValue = $sekolah->sekolah_id;
            }
        } else {
            $instansiValue  = $validated['Instansi'];
            $sekolahIdValue = null;
        }

        // ── Cek peserta via NIK saja ─────────────────────────────────────────
        $peserta = Peserta::where('nik', $validated['nik'])->first();

        if (!$peserta) {
            // Buat peserta baru jika NIK belum ada
            $peserta = Peserta::create([
                'nama'          => $validated['nama'],
                'nip'           => $validated['nip'] ?? null,
                'nik'           => $validated['nik'],
                'email'         => $validated['email'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'kota_id'       => $validated['kota_id'] ?? null,
                'sekolah_id'    => $sekolahIdValue,
                'Instansi'      => $instansiValue,
            ]);
        } else {
            // NIK sudah ada → update semua field yang mungkin berubah
            $peserta->update([
                'nama'          => $validated['nama'],
                'nip'           => $validated['nip'] ?? $peserta->nip,
                'email'         => $validated['email'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'kota_id'       => $validated['kota_id'] ?? $peserta->kota_id,
                'sekolah_id'    => $sekolahIdValue ?? $peserta->sekolah_id,
                'Instansi'      => $instansiValue ?? $peserta->Instansi,
            ]);
        }

        // ── Cek apakah peserta sudah terdaftar di kegiatan ini ───────────────
        $sudahDaftar = PesertaKegiatan::where('peserta_id', $peserta->peserta_id)
            ->where('kegiatan_id', $kegiatan->kegiatan_id)
            ->first();

        if ($sudahDaftar) {
            return back()
                ->withInput()
                ->with('error', 'Akun Anda sudah terdaftar di kegiatan ini.');
        }

        // ── Tentukan status pendaftaran ──────────────────────────────────────
        $statusPendaftaran = 'disetujui';

        if (
            $kegiatan->jenisKegiatan &&
            strtolower($kegiatan->jenisKegiatan->jenis_kegiatan) === 'terbimbing'
        ) {
            $statusPendaftaran = 'menunggu';
        }

        // ── Simpan ke tabel pivot peserta_kegiatan ───────────────────────────
        $pesertaKegiatan = PesertaKegiatan::create([
            'peserta_id'   => $peserta->peserta_id,
            'kegiatan_id'  => $kegiatan->kegiatan_id,
            'status'       => $statusPendaftaran,
            'terdaftar_at' => now(),
        ]);

        // ── Kirim Email Notifikasi ───────────────────────────────────────────
        $this->sendEmailPendaftaran($peserta, $kegiatan);

        return redirect()
            ->route('kegiatan.daftar.success', [
                'slug'            => $kegiatan->slug,
                'pesertaKegiatan' => $this->encodeId($pesertaKegiatan->peserta_kegiatan_id)
            ])
            ->with('success', 'Pendaftaran berhasil dikirim.');
    }

    // ─── Halaman Sukses ──────────────────────────────────────────────────────────

    // Menjadi:
    public function success(string $slug, string $hash)
    {
        // Decode hash ke ID
        $id = $this->decodeId($hash);

        if (!$id) {
            abort(404);
        }

        // Cari pesertaKegiatan berdasarkan ID
        $pesertaKegiatan = PesertaKegiatan::with(['peserta', 'kegiatan.tipeKegiatan', 'kegiatan.jenisKegiatan', 'kegiatan.moda', 'kegiatan.fasilitators'])
            ->where('peserta_kegiatan_id', $id)
            ->firstOrFail();

        // Pastikan kegiatan sesuai dengan slug
        if ($pesertaKegiatan->kegiatan->slug !== $slug) {
            abort(404);
        }

        // CEK STATUS URL - Jika inactive, tampilkan halaman tidak tersedia
        if (($pesertaKegiatan->kegiatan->status_url ?? 'active') === 'inactive') {
            abort(403, 'Maaf, halaman pendaftaran kegiatan ini sedang tidak tersedia.');
        }

        return view('pendaftaran.success', compact('pesertaKegiatan'));
    }

    // ─── AJAX: Cari Sekolah ──────────────────────────────────────────────────────

    public function searchSekolah(Request $request)
    {
        $keyword = $request->input('q', '');
        $kotaId  = $request->input('kota_id', '');

        $query = Sekolah::query();

        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_sekolah', 'like', '%' . $keyword . '%')
                    ->orWhere('npsn', 'like', '%' . $keyword . '%');
            });
        }

        if (!empty($kotaId)) {
            $kota = Kota::find($kotaId);
            if ($kota) {
                $query->where('kab_kota', 'like', '%' . $kota->nama_kota . '%');
            }
        }

        $sekolah = $query
            ->select('sekolah_id', 'npsn', 'nama_sekolah', 'kab_kota', 'alamat', 'jenjang')
            ->orderBy('nama_sekolah')
            ->limit(20)
            ->get();

        return response()->json($sekolah);
    }


    // ── Private: Kirim Email Pendaftaran ─────────────────────────────────────
    private function sendEmailPendaftaran(Peserta $peserta, Kegiatan $kegiatan): void
    {
        try {
            $waktuText = '-';
            if (!empty($kegiatan->start_date) || !empty($kegiatan->end_date)) {
                $start     = $kegiatan->start_date ? \Carbon\Carbon::parse($kegiatan->start_date)->format('d M Y') : '-';
                $end       = $kegiatan->end_date   ? \Carbon\Carbon::parse($kegiatan->end_date)->format('d M Y')   : '-';
                $waktuText = $start . ' s/d ' . $end;
            }

            $namaKegiatan  = $kegiatan->nama_kegiatan ?? '-';
            $tipeKegiatan  = optional($kegiatan->tipeKegiatan)->nama_kegiatan ?? '-';
            $jenisKegiatan = optional($kegiatan->jenisKegiatan)->jenis_kegiatan ?? '-';
            $moda          = optional($kegiatan->moda)->jenis_moda ?? '-';
            $token         = $kegiatan->token_kegiatan ?? '-';
            $loginUrl      = route('kegiatan.login.form', $kegiatan->slug);
            $nip           = $peserta->nip ?? '-';
            $nama          = $peserta->nama;

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME');
            $mail->Password   = env('MAIL_PASSWORD');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = (int) env('MAIL_PORT', 587);
            $mail->CharSet    = 'UTF-8';
            $mail->Timeout    = 30;
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true,
                ],
            ];

            $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME', 'MOOC BGTK Banten'));
            $mail->addAddress($peserta->email, $peserta->nama);

            $mail->isHTML(true);
            $mail->Subject = 'Pendaftaran Berhasil - ' . $namaKegiatan;
            $mail->Body    = "
            <!DOCTYPE html>
            <html>
            <head>
              <meta charset='UTF-8'>
              <style>
                body { font-family: Arial, sans-serif; background: #f5f7fa; margin: 0; padding: 0; }
                .wrap { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
                .header { background: linear-gradient(135deg, #0d56b6, #074a9a); padding: 32px 28px; color: #fff; }
                .header h1 { margin: 0 0 6px; font-size: 22px; }
                .header p { margin: 0; opacity: .85; font-size: 14px; }
                .body { padding: 28px; }
                .info-box { background: #f3f7ff; border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid #e6ebf4; }
                .info-box h3 { margin: 0 0 14px; color: #0a2f6b; font-size: 15px; }
                .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e6ebf4; font-size: 13px; }
                .info-row:last-child { border-bottom: none; }
                .info-row span:first-child { color: #5b6474; font-weight: 600; }
                .info-row span:last-child { color: #0a2f6b; font-weight: 700; }
                .login-box { background: #e9fff1; border: 1px solid #bff0ce; border-radius: 12px; padding: 20px; margin-bottom: 20px; }
                .login-box h3 { margin: 0 0 10px; color: #146c2e; font-size: 15px; }
                .login-box p { margin: 0 0 6px; font-size: 13px; color: #333; }
                .btn { display: inline-block; padding: 12px 28px; background: #0d56b6; color: #fff; border-radius: 999px; text-decoration: none; font-weight: 700; font-size: 14px; margin-top: 14px; }
                .footer { padding: 18px 28px; background: #f8fbff; text-align: center; color: #9aa6ba; font-size: 12px; }
              </style>
            </head>
            <body>
              <div class='wrap'>
                <div class='header'>
                  <h1>Pendaftaran Berhasil! 🎉</h1>
                  <p>Halo {$nama}, pendaftaran Anda telah diterima.</p>
                </div>
                <div class='body'>
                  <div class='info-box'>
                    <h3>📋 Detail Kegiatan</h3>
                    <div class='info-row'><span>Nama Kegiatan :</span><span>{$namaKegiatan}</span></div>
                    <div class='info-row'><span>Tipe Kegiatan :</span><span>{$tipeKegiatan}</span></div>
                    <div class='info-row'><span>Jenis Kegiatan :</span><span>{$jenisKegiatan}</span></div>
                    <div class='info-row'><span>Moda :</span><span>{$moda}</span></div>
                    <div class='info-row'><span>Waktu :</span><span>{$waktuText}</span></div>
                  </div>
                  <div class='login-box'>
                    <h3>🔐 Info Login Kegiatan</h3>
                    <p><strong>NIP :</strong> {$nip}</p>
                    <p><strong>Token :</strong> Pantau IG BGTK Provinsi Banten</p>
                    <p style='font-size:12px;color:#5b6474;margin-top:8px;'>Gunakan NIP dan Token di atas untuk login ke halaman kegiatan.</p>
                    <a href='{$loginUrl}' class='btn'>Login ke Kegiatan</a>
                  </div>
                  <p style='font-size:13px;color:#5b6474;'>Email ini dikirim otomatis, harap tidak membalas.</p>
                </div>
                <div class='footer'>© MOOC BGTK Banten — Sistem Pendaftaran Kegiatan</div>
              </div>
            </body>
            </html>
            ";

            $mail->send();
        } catch (\Exception $e) {
            \Log::error('Gagal kirim email pendaftaran: ' . $e->getMessage());
        }
    }
}

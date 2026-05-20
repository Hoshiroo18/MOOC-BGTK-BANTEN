<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\TipeKegiatan;
use App\Models\JenisKegiatan;
use App\Models\Moda;
use App\Models\Fasilitator;
use App\Models\PesertaKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KegiatanController extends Controller
{
    private function checkAdmin()
    {
        abort_if(!in_array(auth()->user()->role_id, [1, 2]), 403);
    }

    public function index()
    {
        $this->checkAdmin();

        $kegiatan = Kegiatan::with(['tipeKegiatan', 'jenisKegiatan', 'moda', 'fasilitators'])
            ->latest()
            ->get();

        $tipeKegiatan  = TipeKegiatan::all();
        $jenisKegiatan = JenisKegiatan::all();
        $modaList      = Moda::all();
        $fasilitators  = Fasilitator::all();

        return view('admin.kegiatan.index', compact(
            'kegiatan',
            'tipeKegiatan',
            'jenisKegiatan',
            'modaList',
            'fasilitators'
        ));
    }

    public function edit(Kegiatan $kegiatan)
    {
        $this->checkAdmin();

        $kegiatan->load(['tipeKegiatan', 'jenisKegiatan', 'moda', 'fasilitators']);

        $tipeKegiatan  = TipeKegiatan::all();
        $jenisKegiatan = JenisKegiatan::all();
        $modaList      = Moda::all();
        $fasilitators  = Fasilitator::all();

        return view('admin.kegiatan.edit', compact(
            'kegiatan',
            'tipeKegiatan',
            'jenisKegiatan',
            'modaList',
            'fasilitators'
        ));
    }

    public function store(Request $request)
    {
        $this->checkAdmin();

        $validated = $this->validateKegiatan($request);

        $slugBase = Str::slug($validated['nama_kegiatan']);
        $slugBase = $slugBase ?: 'kegiatan';

        $slug    = $slugBase;
        $counter = 1;

        while (Kegiatan::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $counter;
            $counter++;
        }

        if ($request->hasFile('flayer')) {
            $validated['flayer'] = $request->file('flayer')
                ->store('flayer-kegiatan', 'public');
        }

        $validated['slug']             = $slug;
        $validated['link_pendaftaran'] = route('kegiatan.daftar', $slug);

        // Set default status_url jika tidak diisi
        if (empty($validated['status_url'])) {
            $validated['status_url'] = 'active';
        }

        $kegiatan = Kegiatan::create($validated);

        if ($request->filled('fasilitator_ids')) {
            $this->syncFasilitators($kegiatan->kegiatan_id, $request->fasilitator_ids);
        }

        return redirect()
            ->route('admin.kegiatan.index')
            ->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        $this->checkAdmin();

        $validated = $this->validateKegiatan($request, true, $kegiatan);

        $slugBase = Str::slug($validated['nama_kegiatan']);
        $slugBase = $slugBase ?: 'kegiatan-' . $kegiatan->kegiatan_id;

        $slug    = $slugBase;
        $counter = 1;

        while (
            Kegiatan::where('slug', $slug)
            ->where('kegiatan_id', '!=', $kegiatan->kegiatan_id)
            ->exists()
        ) {
            $slug = $slugBase . '-' . $counter;
            $counter++;
        }

        $validated['slug']             = $slug;
        $validated['link_pendaftaran'] = route('kegiatan.daftar', $slug);

        if ($request->hasFile('flayer')) {
            if ($kegiatan->flayer && Storage::disk('public')->exists($kegiatan->flayer)) {
                Storage::disk('public')->delete($kegiatan->flayer);
            }

            $validated['flayer'] = $request->file('flayer')
                ->store('flayer-kegiatan', 'public');
        }

        $kegiatan->update($validated);

        if ($request->has('fasilitator_ids')) {
            $this->syncFasilitators($kegiatan->kegiatan_id, $request->fasilitator_ids ?? []);
        }

        return redirect()
            ->route('admin.kegiatan.index')
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    // Method baru untuk update status URL (Active/Inactive)
    public function updateStatus(Request $request, Kegiatan $kegiatan)
    {
        $this->checkAdmin();

        $request->validate([
            'status_url' => 'required|in:active,inactive',
        ]);

        $kegiatan->update([
            'status_url' => $request->status_url,
        ]);

        $statusText = $request->status_url === 'active' ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()
            ->route('admin.kegiatan.index')
            ->with('success', "Status URL kegiatan berhasil {$statusText}.");
    }

    // Method baru untuk update token kegiatan
    public function updateToken(Request $request, Kegiatan $kegiatan)
    {
        $this->checkAdmin();

        $request->validate([
            'token_kegiatan' => 'nullable|string|max:10',
        ]);

        $kegiatan->update([
            'token_kegiatan' => $request->token_kegiatan,
        ]);

        return redirect()
            ->route('admin.kegiatan.index')
            ->with('success', 'Token kegiatan berhasil diperbarui.');
    }

    public function markMoodleInjected(Kegiatan $kegiatan)
    {
        $this->checkAdmin();

        if (!$kegiatan->link_lms) {
            return back()->withErrors([
                'link_lms' => 'Link LMS/Moodle belum diisi.',
            ]);
        }

        $jumlah = PesertaKegiatan::where('kegiatan_id', $kegiatan->kegiatan_id)
            ->where('status', 'menunggu')
            ->update([
                'status' => 'disetujui',
            ]);

        return back()->with('success', 'Berhasil menyetujui ' . $jumlah . ' peserta.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        $this->checkAdmin();

        if ($kegiatan->flayer && Storage::disk('public')->exists($kegiatan->flayer)) {
            Storage::disk('public')->delete($kegiatan->flayer);
        }

        // Hapus fasilitator mapping
        DB::table('fasilitator_mapping')
            ->where('kegiatan_id', $kegiatan->kegiatan_id)
            ->delete();

        // Hapus peserta_kegiatan yang terkait
        PesertaKegiatan::where('kegiatan_id', $kegiatan->kegiatan_id)->delete();

        $kegiatan->delete();

        return redirect()
            ->route('admin.kegiatan.index')
            ->with('success', 'Kegiatan berhasil dihapus.');
    }

    // ─── Private Helpers ────────────────────────────────────────────────────────

    private function syncFasilitators(int $kegiatanId, array $fasilitatorIds): void
    {
        DB::table('fasilitator_mapping')
            ->where('kegiatan_id', $kegiatanId)
            ->delete();

        $rows = array_map(fn($id) => [
            'kegiatan_id'    => $kegiatanId,
            'fasilitator_id' => $id,
        ], $fasilitatorIds);

        if (!empty($rows)) {
            DB::table('fasilitator_mapping')->insert($rows);
        }
    }

    private function validateKegiatan(Request $request, bool $isUpdate = false, ?Kegiatan $kegiatan = null): array
    {
        $flayerRule = $isUpdate && $kegiatan && $kegiatan->flayer
            ? 'nullable'
            : 'required';

        return $request->validate([
            'tipe_kegiatan_id'  => 'required|integer|exists:tipe_kegiatan,tipe_kegiatan_id',
            'jenis_kegiatan_id' => 'nullable|integer|exists:jenis_kegiatan,jenis_kegiatan_id',
            'moda_id'           => 'required|integer|exists:moda,moda_id',
            'kuota'             => 'required|integer|min:1',
            'is_registration_required' => 'nullable|boolean',
            'waktu_pelaksanaan' => 'nullable|date',
            'start_date'        => 'nullable|date',
            'end_date'          => 'nullable|date|after_or_equal:start_date',
            'nama_kegiatan'     => 'required|string|max:255',
            'deskripsi'         => 'required|string',
            'link_zoom'         => 'nullable|string|max:500',
            'link_lms'          => 'nullable|string|max:255',
            'token_kegiatan'    => 'nullable|string|max:10', // Token manual input
            'status_url'        => 'nullable|in:active,inactive', // Status URL
            'fasilitator_ids'   => 'nullable|array',
            'fasilitator_ids.*' => 'integer|exists:fasilitator,fasilitator_id',
            'flayer'            => [
                $flayerRule,
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
                'dimensions:width=1080,height=1350',
            ],
        ], [
            // PENAMBAHAN PESAN ERROR MANUAL
            'flayer.dimensions' => 'Ukuran gambar flayer tidak sesuai. mohon unggah gambar dengan ukuran 1080x1350.',
            'flayer.required'   => 'Flayer wajib diunggah.',
            'flayer.image'      => 'File yang diunggah harus berupa gambar.',
            'flayer.mimes'      => 'Format gambar harus jpg, jpeg, png, atau webp.',
            'flayer.max'        => 'Ukuran file gambar maksimal 5MB.'
        ]);
    }
}
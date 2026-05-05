<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KegiatanController extends Controller
{
    public function index()
    {
        abort_if(auth()->user()->role !== 'admin', 403);

        $kegiatan = Kegiatan::withCount('kelas')->latest()->get();

        return view('admin.kegiatan.index', compact('kegiatan'));
    }

    public function edit(Kegiatan $kegiatan)
    {
        abort_if(auth()->user()->role !== 'admin', 403);

        return view('admin.kegiatan.edit', compact('kegiatan'));
    }

    public function store(Request $request)
    {
        abort_if(auth()->user()->role !== 'admin', 403);

        $validated = $this->validateKegiatan($request);
        $validated = $this->normalizeKegiatanPayload($validated, $request);

        $slugBase = Str::slug($validated['nama_kegiatan']);

        if ($slugBase === '') {
            $slugBase = 'kegiatan';
        }

        $slug = $slugBase;
        $counter = 1;

        while (Kegiatan::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $counter;
            $counter++;
        }

        if ($request->hasFile('flayer')) {
            $validated['flayer'] = $request->file('flayer')->store('flayer-kegiatan', 'public');
        }

        $validated['slug'] = $slug;
        $validated['link_pendaftaran'] = route('kegiatan.daftar', $slug);

        Kegiatan::create($validated);

        return redirect()
            ->route('admin.kegiatan.index')
            ->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        abort_if(auth()->user()->role !== 'admin', 403);

        $validated = $this->validateKegiatan($request, true, $kegiatan);
        $validated = $this->normalizeKegiatanPayload($validated, $request);

        $slugBase = Str::slug($validated['nama_kegiatan']);

        if ($slugBase === '') {
            $slugBase = 'kegiatan-' . $kegiatan->id;
        }

        $slug = $slugBase;
        $counter = 1;

        while (
            Kegiatan::where('slug', $slug)
                ->where('id', '!=', $kegiatan->id)
                ->exists()
        ) {
            $slug = $slugBase . '-' . $counter;
            $counter++;
        }

        $validated['slug'] = $slug;
        $validated['link_pendaftaran'] = route('kegiatan.daftar', $slug);

        if ($request->hasFile('flayer')) {
            if ($kegiatan->flayer && Storage::disk('public')->exists($kegiatan->flayer)) {
                Storage::disk('public')->delete($kegiatan->flayer);
            }

            $validated['flayer'] = $request->file('flayer')->store('flayer-kegiatan', 'public');
        }

        $kegiatan->update($validated);

        return redirect()
            ->route('admin.kegiatan.index')
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function markMoodleInjected(Kegiatan $kegiatan)
    {
        abort_if(auth()->user()->role !== 'admin', 403);

        if ($kegiatan->jenis_pelatihan !== 'terbimbing') {
            return back()->withErrors([
                'inject' => 'Inject Moodle hanya untuk kegiatan dengan jenis pelatihan terbimbing.',
            ]);
        }

        if (!$kegiatan->moodle_course_url) {
            return back()->withErrors([
                'moodle_course_url' => 'Link course Moodle belum diisi.',
            ]);
        }

        $jumlah = $kegiatan->kelas()
            ->whereNull('moodle_injected_at')
            ->update([
                'status_pendaftaran' => 'disetujui',
                'moodle_injected_at' => now(),
                'moodle_injected_by' => auth()->id(),
            ]);

        return back()->with('success', 'Berhasil mengaktifkan link Moodle untuk ' . $jumlah . ' peserta.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        abort_if(auth()->user()->role !== 'admin', 403);

        if ($kegiatan->flayer && Storage::disk('public')->exists($kegiatan->flayer)) {
            Storage::disk('public')->delete($kegiatan->flayer);
        }

        $kegiatan->delete();

        return redirect()
            ->route('admin.kegiatan.index')
            ->with('success', 'Kegiatan berhasil dihapus.');
    }

    private function validateKegiatan(Request $request, bool $isUpdate = false, ?Kegiatan $kegiatan = null): array
    {
        $flayerRule = $isUpdate && $kegiatan && $kegiatan->flayer
            ? 'nullable'
            : 'required';

        return $request->validate([
            'jenis_kegiatan' => 'required|in:webinar,pelatihan,konsultasi',
            'moda' => 'required|in:luring,daring,hybrid',
            'jenis_pelatihan' => 'nullable|in:terbimbing,mandiri',
            'perlu_pendaftaran' => 'nullable|boolean',

            'fasil' => 'required|string|max:255',
            'kuota' => 'required|integer|min:1',
            'waktu_pelaksanaan' => 'required|date',
            'nama_kegiatan' => 'required|string|max:255',
            'deskripsi' => 'required|string',

            'link_zoom' => 'nullable|string|max:255',
            'moodle_course_url' => 'nullable|string|max:255',

            'flayer' => [
                $flayerRule,
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
                'dimensions:width=1080,height=1350',
            ],
        ]);
    }

    private function normalizeKegiatanPayload(array $validated, Request $request): array
    {
        $validated['perlu_pendaftaran'] = $request->boolean('perlu_pendaftaran');

        if (($validated['jenis_pelatihan'] ?? null) === 'terbimbing') {
            $validated['perlu_pendaftaran'] = true;
        }

        if (($validated['jenis_pelatihan'] ?? null) === 'mandiri') {
            $validated['perlu_pendaftaran'] = false;
        }

        return $validated;
    }
}
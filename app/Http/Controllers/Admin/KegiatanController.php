<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class KegiatanController extends Controller
{
    public function index()
    {
        $kegiatan = Kegiatan::latest()->get();

        return view('admin.kegiatan.index', compact('kegiatan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_kegiatan' => 'required|in:webinar,pelatihan,konsultasi',
            'moda' => 'required|in:luring,daring,hybrid',
            'fasil' => 'nullable|string|max:255',
            'kuota' => 'required|integer|min:1',
            'waktu_pelaksanaan' => 'required|date',
            'nama_kegiatan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'link_zoom' => 'nullable|string|max:255',
            'flayer' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120|dimensions:width=1080,height=1350',
        ]);

        $slugBase = Str::slug($validated['nama_kegiatan']);
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
        $validated['link_pendaftaran'] = url('/daftar-kegiatan/' . $slug);

        Kegiatan::create($validated);

        return redirect()
            ->route('admin.kegiatan.index')
            ->with('success', 'Kegiatan berhasil ditambahkan.');
    }
    public function destroy(Kegiatan $kegiatan)
{
    if ($kegiatan->flayer && Storage::disk('public')->exists($kegiatan->flayer)) {
        Storage::disk('public')->delete($kegiatan->flayer);
    }

    $kegiatan->delete();

    return redirect()
        ->route('admin.kegiatan.index')
        ->with('success', 'Kegiatan berhasil dihapus.');
}
}
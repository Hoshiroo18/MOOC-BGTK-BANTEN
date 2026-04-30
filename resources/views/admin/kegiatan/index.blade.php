@extends('layouts.app')

@section('title', 'Kegiatan - MOOC BGTK Banten')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/kegiatan.css') }}">
@endpush

@section('content')

@php
  $kegiatanCollection = method_exists($kegiatan, 'getCollection') ? $kegiatan->getCollection() : collect($kegiatan);

  $totalKegiatan = method_exists($kegiatan, 'total') ? $kegiatan->total() : $kegiatanCollection->count();
  $totalWebinar = $kegiatanCollection->where('jenis_kegiatan', 'webinar')->count();
  $totalPelatihan = $kegiatanCollection->where('jenis_kegiatan', 'pelatihan')->count();
  $totalKonsultasi = $kegiatanCollection->where('jenis_kegiatan', 'konsultasi')->count();
@endphp

<section class="admin-kegiatan-page">

  {{-- HERO ATAS --}}
  <div class="kegiatan-hero">
    <div class="container kegiatan-hero-inner">

      <div class="kegiatan-hero-copy">
        <span class="eyebrow light">Manajemen Kegiatan</span>

        <h1>Kelola Kegiatan MOOC BGTK Banten</h1>

        <p>
          Tambahkan webinar, pelatihan, dan konsultasi dalam satu dashboard.
          Data kegiatan, flayer, kuota, jadwal, dan link pendaftaran dapat
          dikelola lebih rapi, cepat, dan terpusat.
        </p>

        <div class="kegiatan-hero-actions">
          <a href="#form-kegiatan" class="hero-main-btn">
            Tambah Kegiatan
          </a>

          <a href="#data-kegiatan" class="hero-outline-btn">
            Lihat Data
          </a>
        </div>

        <div class="kegiatan-hero-stats">
          <div>
            <strong>{{ $totalKegiatan }}</strong>
            <span>Total Kegiatan</span>
          </div>

          <div>
            <strong>{{ $totalWebinar }}</strong>
            <span>Webinar</span>
          </div>

          <div>
            <strong>{{ $totalPelatihan }}</strong>
            <span>Pelatihan</span>
          </div>

          <div>
            <strong>{{ $totalKonsultasi }}</strong>
            <span>Konsultasi</span>
          </div>
        </div>
      </div>

      <div class="kegiatan-hero-panel">

        <div class="hero-panel-card main-preview-card">
          <div class="preview-card-top">
            <div>
              <span>Dashboard Admin</span>
              <h3>Publikasi Kegiatan</h3>
            </div>

            <div class="preview-icon">
              ✦
            </div>
          </div>

          <div class="preview-progress">
            <div>
              <span>Data Kegiatan</span>
              <strong>{{ $totalKegiatan }} tersimpan</strong>
            </div>

            <div class="progress-bar">
              <span></span>
            </div>
          </div>

          <div class="preview-mini-list">
            <div>
              <span class="mini-dot webinar"></span>
              <p>Webinar daring</p>
            </div>

            <div>
              <span class="mini-dot pelatihan"></span>
              <p>Pelatihan kompetensi</p>
            </div>

            <div>
              <span class="mini-dot konsultasi"></span>
              <p>Konsultasi pembinaan</p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <div class="container kegiatan-main-container">

    @if(session('success'))
      <div class="alert-success">
        {{ session('success') }}
      </div>
    @endif

    @if($errors->any())
      <div class="alert-error">
        {{ $errors->first() }}
      </div>
    @endif

    <div class="kegiatan-grid">

      <div class="admin-card kegiatan-form-card" id="form-kegiatan">
        <div class="section-heading compact">
          <div>
            <span class="card-kicker">Input Data</span>
            <h2>Form Kegiatan</h2>
            <p>Isi data kegiatan yang akan dipublikasikan ke halaman user.</p>
          </div>
        </div>

        <form action="{{ route('admin.kegiatan.store') }}" method="POST" enctype="multipart/form-data" class="admin-form">
          @csrf

          <div class="form-row">
            <div class="form-group">
              <label for="jenis_kegiatan">Jenis Kegiatan</label>
              <select id="jenis_kegiatan" name="jenis_kegiatan" required>
                <option value="">Pilih jenis kegiatan</option>
                <option value="webinar" {{ old('jenis_kegiatan') == 'webinar' ? 'selected' : '' }}>Webinar</option>
                <option value="pelatihan" {{ old('jenis_kegiatan') == 'pelatihan' ? 'selected' : '' }}>Pelatihan</option>
                <option value="konsultasi" {{ old('jenis_kegiatan') == 'konsultasi' ? 'selected' : '' }}>Konsultasi</option>
              </select>
            </div>

            <div class="form-group">
              <label for="moda">Moda</label>
              <select id="moda" name="moda" required>
                <option value="">Pilih moda</option>
                <option value="luring" {{ old('moda') == 'luring' ? 'selected' : '' }}>Luring</option>
                <option value="daring" {{ old('moda') == 'daring' ? 'selected' : '' }}>Daring</option>
                <option value="hybrid" {{ old('moda') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="nama_kegiatan">Nama Kegiatan</label>
            <input
              type="text"
              id="nama_kegiatan"
              name="nama_kegiatan"
              value="{{ old('nama_kegiatan') }}"
              placeholder="Contoh: Webinar Implementasi Kurikulum Merdeka"
              required
            >
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="fasil">Fasil</label>
              <input
                type="text"
                id="fasil"
                name="fasil"
                value="{{ old('fasil') }}"
                placeholder="Contoh: Dr. Yusup Ardabili"
              >
            </div>

            <div class="form-group">
              <label for="kuota">Kuota</label>
              <input
                type="number"
                id="kuota"
                name="kuota"
                value="{{ old('kuota') }}"
                placeholder="Contoh: 100"
                min="1"
                required
              >
            </div>
          </div>

          <div class="form-group">
            <label for="waktu_pelaksanaan">Waktu Pelaksanaan</label>
            <input
              type="datetime-local"
              id="waktu_pelaksanaan"
              name="waktu_pelaksanaan"
              value="{{ old('waktu_pelaksanaan') }}"
              required
            >
          </div>

          <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea
              id="deskripsi"
              name="deskripsi"
              rows="4"
              placeholder="Tulis deskripsi kegiatan..."
            >{{ old('deskripsi') }}</textarea>
          </div>

          <div class="form-group">
            <label for="link_zoom">Link Zoom</label>
            <input
              type="text"
              id="link_zoom"
              name="link_zoom"
              value="{{ old('link_zoom') }}"
              placeholder="https://zoom.us/..."
            >
          </div>

          <div class="form-group">
            <label for="flayer">Flayer</label>
            <input
              type="file"
              id="flayer"
              name="flayer"
              accept="image/*"
            >
            <small class="form-help">
              Gunakan gambar poster/flayer kegiatan agar tampilan publikasi lebih menarik.
            </small>
          </div>

          <div class="form-group">
            <label>Link Pendaftaran</label>
            <input
              type="text"
              value="Auto generate setelah kegiatan disimpan"
              readonly
            >
          </div>

          <button type="submit" class="admin-submit-btn">
            Simpan Kegiatan
          </button>
        </form>
      </div>

      <div class="admin-card kegiatan-data-card" id="data-kegiatan">
        <div class="section-heading compact">
          <div>
            <span class="card-kicker">Database</span>
            <h2>Data Kegiatan</h2>
            <p>Data kegiatan yang sudah tersimpan dan siap dipublikasikan.</p>
          </div>
        </div>

        <div class="kegiatan-list">
          @forelse($kegiatan as $item)
            <article class="kegiatan-item">
              @if($item->flayer)
                <div class="kegiatan-flayer-wrap">
                  <img
                    src="{{ asset('storage/' . $item->flayer) }}"
                    alt="{{ $item->nama_kegiatan }}"
                    class="kegiatan-flayer"
                  >
                </div>
              @else
                <div class="kegiatan-flayer-wrap kegiatan-flayer-empty">
                  <span>MOOC</span>
                </div>
              @endif

              <div class="kegiatan-item-body">
                <div class="kegiatan-badges">
                  <span>{{ ucfirst($item->jenis_kegiatan) }}</span>
                  <span>{{ ucfirst($item->moda) }}</span>
                </div>

                <h3>{{ $item->nama_kegiatan }}</h3>

                <p>
                  {{ \Illuminate\Support\Str::limit($item->deskripsi, 110) }}
                </p>

                <div class="kegiatan-meta">
                  <small>Fasil: {{ $item->fasil ?? '-' }}</small>
                  <small>Kuota: {{ $item->kuota }}</small>
                  <small>Waktu: {{ \Carbon\Carbon::parse($item->waktu_pelaksanaan)->format('d M Y H:i') }}</small>
                </div>

                <div class="kegiatan-link-box">
                  <input type="text" value="{{ $item->link_pendaftaran }}" readonly>
                  <a href="{{ $item->link_pendaftaran }}" target="_blank">Buka</a>
                </div>

                <form
                  action="{{ route('admin.kegiatan.destroy', $item->id) }}"
                  method="POST"
                  class="delete-kegiatan-form"
                  onsubmit="return confirm('Yakin mau hapus kegiatan ini?')"
                >
                  @csrf
                  @method('DELETE')

                  <button type="submit" class="delete-kegiatan-btn">
                    Hapus Kegiatan
                  </button>
                </form>
              </div>
            </article>
          @empty
            <div class="empty-state">
              Belum ada kegiatan. Tambahkan kegiatan dari form sebelah kiri.
            </div>
          @endforelse
        </div>
      </div>

    </div>

  </div>
</section>

@endsection
@extends('layouts.app')

@section('title', 'Admin Dashboard - MOOC BGTK Banten')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
@endpush

@section('content')

<section class="admin-profile-page">

  {{-- HERO SPLIT --}}
  <section class="admin-hero-split">
    <div class="admin-hero-left-panel">
      <div class="container">
        <div class="admin-hero-copy">
          <span class="hero-badge-label">Admin Panel</span>

          <h1>Profil Aplikasi MOOC BGTK Banten</h1>

          <p>
            Platform digital untuk mendukung pengelolaan kegiatan pembelajaran,
            webinar, pelatihan, konsultasi, pendaftaran peserta, serta publikasi
            informasi kegiatan secara lebih rapi dan terpusat.
          </p>

          <div class="admin-hero-buttons">
            <a href="{{ route('admin.kegiatan.index') }}" class="hero-main-btn">
              Kelola Kegiatan
            </a>

            <a href="{{ url('/dashboard') }}" class="hero-outline-btn">
              Lihat Halaman User
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="admin-hero-right-panel">
      <img
        src="{{ asset('images/gam4.jpeg') }}"
        alt="MOOC BGTK Banten"
        class="hero-split-image"
      >
    </div>
  </section>

  {{-- CONTENT --}}
  <div class="container admin-profile-content-wrap">

    <div class="profile-stats-row">
      <div class="profile-stat-card">
        <strong>01</strong>
        <span>Manajemen Kegiatan</span>
      </div>

      <div class="profile-stat-card">
        <strong>02</strong>
        <span>Pendaftaran Online</span>
      </div>

      <div class="profile-stat-card">
        <strong>03</strong>
        <span>Publikasi Flayer</span>
      </div>

      <div class="profile-stat-card">
        <strong>04</strong>
        <span>Dokumentasi Digital</span>
      </div>
    </div>

    <div class="profile-app-grid">
      <div class="profile-app-card wide-card">
        <span class="card-kicker">Tentang Aplikasi</span>

        <h2>MOOC BGTK Banten</h2>

        <p>
          Sistem ini dirancang untuk membantu admin dalam mengelola data kegiatan
          seperti webinar, pelatihan, dan konsultasi. Setiap kegiatan dapat memiliki
          informasi lengkap seperti jenis kegiatan, moda pelaksanaan, fasilitator,
          kuota, waktu pelaksanaan, deskripsi, link Zoom, flayer, dan link
          pendaftaran otomatis.
        </p>

        <p>
          Dengan adanya aplikasi ini, proses publikasi kegiatan menjadi lebih mudah
          karena data kegiatan tersimpan di database dan dapat dikelola melalui
          dashboard admin.
        </p>
      </div>

      <div class="profile-app-card">
        <span class="card-kicker">Fitur Utama</span>

        <h3>Yang Bisa Dikelola</h3>

        <div class="feature-list">
          <div>
            <strong>Manajemen Kegiatan</strong>
            <span>Admin dapat membuat, melihat, dan menghapus data kegiatan.</span>
          </div>

          <div>
            <strong>Link Pendaftaran Otomatis</strong>
            <span>Sistem membuat link pendaftaran berdasarkan nama kegiatan.</span>
          </div>

          <div>
            <strong>Upload Flayer</strong>
            <span>Admin dapat mengunggah flayer kegiatan sesuai ukuran publikasi.</span>
          </div>
        </div>
      </div>
    </div>

    <div class="profile-purpose-section">
      <div>
        <span class="card-kicker">Tujuan Sistem</span>

        <h2>Membantu pengelolaan kegiatan secara digital.</h2>
      </div>

      <div class="purpose-list">
        <div class="purpose-item">
          <span>✓</span>
          <p>Mempermudah admin membuat data kegiatan baru.</p>
        </div>

        <div class="purpose-item">
          <span>✓</span>
          <p>Menyimpan data kegiatan ke database agar lebih terstruktur.</p>
        </div>

        <div class="purpose-item">
          <span>✓</span>
          <p>Menyediakan link pendaftaran otomatis untuk setiap kegiatan.</p>
        </div>

        <div class="purpose-item">
          <span>✓</span>
          <p>Mendukung publikasi kegiatan dengan flayer digital.</p>
        </div>
      </div>
    </div>

  </div>
</section>

@endsection
@extends('layouts.app')

@section('title', 'Dashboard - MOOC BGTK Banten')

@section('content')

<section class="hero-section">
  <div class="container hero-grid">
    <div class="hero-content">
      <span class="eyebrow">Dashboard Belajar</span>
      <h2>Selamat datang kembali di kelas online kamu.</h2>
      <p>
        Pantau progres belajar, lanjutkan materi terakhir, dan kelola sertifikat pembelajaran
        dalam satu halaman.
      </p>

      <div class="hero-actions">
        <a href="#" class="btn btn-primary">Lanjutkan Belajar</a>
        <a href="#" class="btn btn-secondary">Lihat Semua Kelas</a>
      </div>
    </div>

    <div class="hero-card">
      <p class="card-label">Progress Utama</p>
      <h3>Frontend Web Dasar</h3>

      <div class="progress-info">
        <span>72% selesai</span>
        <strong>8/11 modul</strong>
      </div>

      <div class="progress-bar">
        <div class="progress-fill"></div>
      </div>

      <small>Terakhir dibuka: Pengenalan Layout CSS</small>
    </div>
  </div>
</section>

<section class="container dashboard-layout">

  <aside class="sidebar">
    <div class="profile-card">
      <div class="profile-avatar">DN</div>
      <h3>Devita Nelaapr</h3>
      <p>Peserta MOOC</p>

      <div class="profile-level">
        <span>Level</span>
        <strong>Intermediate</strong>
      </div>
    </div>

    <div class="side-menu">
      <a href="#" class="active">Overview</a>
      <a href="#">Kelas Saya</a>
      <a href="#">Tugas</a>
      <a href="#">Sertifikat</a>
      <a href="#">Pengaturan</a>
    </div>
  </aside>

  <section class="content">

    <div class="stats-grid">
      <div class="stat-card">
        <span>Kelas Aktif</span>
        <strong>4</strong>
        <p>2 kelas sedang berjalan</p>
      </div>

      <div class="stat-card">
        <span>Sertifikat</span>
        <strong>3</strong>
        <p>Siap diunduh</p>
      </div>

      <div class="stat-card">
        <span>Total Jam Belajar</span>
        <strong>28</strong>
        <p>Naik 6 jam minggu ini</p>
      </div>
    </div>

    <div class="section-heading">
      <div>
        <h2>Kelas yang Sedang Dipelajari</h2>
        <p>Lanjutkan pembelajaran terakhir kamu.</p>
      </div>
      <a href="#">Lihat semua</a>
    </div>

    <div class="course-grid">
      <article class="course-card">
        <div class="course-badge">Web</div>
        <h3>Belajar Dasar Pemrograman Web</h3>
        <p>HTML, CSS, layouting, dan dasar responsif untuk membuat halaman web modern.</p>

        <div class="course-progress">
          <span>72%</span>
          <div class="mini-bar">
            <div style="width:72%"></div>
          </div>
        </div>

        <a href="#" class="course-link">Lanjutkan kelas →</a>
      </article>

      <article class="course-card">
        <div class="course-badge">Laravel</div>
        <h3>Membangun Aplikasi dengan Laravel</h3>
        <p>Pelajari routing, Blade, controller, migration, dan struktur project Laravel.</p>

        <div class="course-progress">
          <span>45%</span>
          <div class="mini-bar">
            <div style="width:45%"></div>
          </div>
        </div>

        <a href="#" class="course-link">Lanjutkan kelas →</a>
      </article>

      <article class="course-card">
        <div class="course-badge">UI/UX</div>
        <h3>Dasar Desain Antarmuka</h3>
        <p>Kenali prinsip warna, spacing, typography, dan komponen dashboard.</p>

        <div class="course-progress">
          <span>88%</span>
          <div class="mini-bar">
            <div style="width:88%"></div>
          </div>
        </div>

        <a href="#" class="course-link">Lanjutkan kelas →</a>
      </article>
    </div>

    <div class="activity-card">
      <div class="section-heading compact">
        <div>
          <h2>Aktivitas Terbaru</h2>
          <p>Riwayat belajar dan pencapaian kamu.</p>
        </div>
      </div>

      <div class="activity-list">
        <div class="activity-item">
          <div class="activity-icon">✓</div>
          <div>
            <h4>Menyelesaikan modul HTML Semantic</h4>
            <p>Hari ini, 10:24 WIB</p>
          </div>
        </div>

        <div class="activity-item">
          <div class="activity-icon">★</div>
          <div>
            <h4>Mendapat badge Konsisten Belajar</h4>
            <p>Kemarin, 19:15 WIB</p>
          </div>
        </div>

        <div class="activity-item">
          <div class="activity-icon">📄</div>
          <div>
            <h4>Mengumpulkan tugas layout dashboard</h4>
            <p>29 April 2026, 14:30 WIB</p>
          </div>
        </div>
      </div>
    </div>

  </section>
</section>

@endsection
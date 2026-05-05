@extends('layouts.app')

@section('title', 'Bantuan - MOOC BGTK Banten')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/bantuan.css') }}">
@endpush

@section('content')

<section class="help-page">

  {{-- HERO --}}
  <section class="help-hero">
    <div class="container help-hero-inner">

      <div class="help-hero-copy">
        <span class="help-badge">Pusat Bantuan</span>

        <h1>Petunjuk Penggunaan MOOC BGTK Banten</h1>

        <p>
          Panduan ini membantu peserta memahami cara memilih kegiatan,
          mengisi pendaftaran, mengakses Zoom atau Moodle, dan melihat kelas
          yang sudah diikuti.
        </p>

        <div class="help-hero-actions">
          <a href="#panduan" class="help-main-btn">
            Lihat Panduan
          </a>

          <a href="#faq" class="help-outline-btn">
            Baca FAQ
          </a>
        </div>

        <div class="help-hero-tags">
          <span>Pendaftaran</span>
          <span>Zoom</span>
          <span>Moodle</span>
          <span>Kelas Saya</span>
        </div>
      </div>

      <div class="help-hero-panel">
        <div class="help-panel-card">
          <span class="panel-kicker">Ringkasan Cepat</span>

          <h2>Alur Mengikuti Kegiatan</h2>

          <div class="help-panel-list">
            <div>
              <strong>1</strong>
              <p>Pilih kegiatan dari halaman dashboard.</p>
            </div>

            <div>
              <strong>2</strong>
              <p>Isi pendaftaran jika kegiatan mewajibkan peserta daftar.</p>
            </div>

            <div>
              <strong>3</strong>
              <p>Tunggu persetujuan admin untuk kegiatan terbimbing.</p>
            </div>

            <div>
              <strong>4</strong>
              <p>Akses Zoom atau Moodle sesuai instruksi kegiatan.</p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>

  {{-- QUICK INFO --}}
  <section class="container help-quick-section">
    <div class="help-quick-grid">

      <article class="help-quick-card">
        <span class="quick-icon">📝</span>
        <h3>Pendaftaran</h3>
        <p>
          Beberapa kegiatan membutuhkan formulir pendaftaran terlebih dahulu.
          Jika tombol pendaftaran tersedia, lengkapi data peserta dengan benar.
        </p>
      </article>

      <article class="help-quick-card">
        <span class="quick-icon">👨‍🏫</span>
        <h3>Terbimbing</h3>
        <p>
          Pada kegiatan terbimbing, akses Moodle aktif setelah admin menyetujui
          peserta dan menginput data peserta ke Moodle.
        </p>
      </article>

      <article class="help-quick-card">
        <span class="quick-icon">🚀</span>
        <h3>Mandiri</h3>
        <p>
          Pada kegiatan mandiri, peserta dapat langsung mengakses Zoom atau
          Moodle jika link sudah tersedia dari admin.
        </p>
      </article>

      <article class="help-quick-card">
        <span class="quick-icon">🔐</span>
        <h3>Akun Peserta</h3>
        <p>
          Peserta dapat login untuk melihat kegiatan yang sedang diikuti,
          riwayat kegiatan, dan status akses kelas.
        </p>
      </article>

    </div>
  </section>

  {{-- PANDUAN --}}
  <section class="container help-content-section" id="panduan">
    <div class="help-section-heading">
      <span class="section-badge">Panduan</span>

      <h2>Cara Mengikuti Kegiatan</h2>

      <p>
        Ikuti langkah berikut agar proses pendaftaran dan akses kegiatan berjalan
        lancar.
      </p>
    </div>

    <div class="help-steps-grid">

      <article class="help-step-card">
        <div class="step-number">01</div>
        <h3>Buka Dashboard</h3>
        <p>
          Masuk ke halaman dashboard umum untuk melihat semua kegiatan yang
          tersedia.
        </p>
      </article>

      <article class="help-step-card">
        <div class="step-number">02</div>
        <h3>Pilih Kegiatan</h3>
        <p>
          Pilih kegiatan yang ingin diikuti, lalu klik tombol
          <b>Lihat Detail</b>.
        </p>
      </article>

      <article class="help-step-card">
        <div class="step-number">03</div>
        <h3>Isi Pendaftaran</h3>
        <p>
          Jika kegiatan membutuhkan pendaftaran, klik tombol
          <b>Isi Pendaftaran</b> dan lengkapi formulir peserta.
        </p>
      </article>

      <article class="help-step-card">
        <div class="step-number">04</div>
        <h3>Baca Instruksi</h3>
        <p>
          Setelah submit, sistem akan menampilkan informasi cara mengikuti
          kegiatan tersebut.
        </p>
      </article>

      <article class="help-step-card">
        <div class="step-number">05</div>
        <h3>Login Peserta</h3>
        <p>
          Login sebagai peserta untuk melihat kegiatan yang sedang diikuti dan
          riwayat kegiatan.
        </p>
      </article>

      <article class="help-step-card">
        <div class="step-number">06</div>
        <h3>Akses Kelas</h3>
        <p>
          Gunakan tombol Zoom atau Moodle yang tersedia di halaman
          <b>Kelas Saya</b>.
        </p>
      </article>

    </div>
  </section>

  {{-- JENIS AKSES --}}
  <section class="container help-content-section">
    <div class="help-section-heading">
      <span class="section-badge">Jenis Akses</span>

      <h2>Perbedaan Akses Kegiatan</h2>

      <p>
        Setiap kegiatan dapat memiliki alur akses berbeda sesuai pengaturan admin.
      </p>
    </div>

    <div class="help-type-grid">

      <article class="help-type-card">
        <div class="type-head">
          <span>📘</span>
          <h3>Konsultasi</h3>
        </div>

        <ul>
          <li>Biasanya menggunakan Zoom sebagai akses utama.</li>
          <li>Tidak selalu membutuhkan Moodle.</li>
          <li>Pendaftaran mengikuti pengaturan dari admin.</li>
        </ul>
      </article>

      <article class="help-type-card">
        <div class="type-head">
          <span>🎥</span>
          <h3>Webinar</h3>
        </div>

        <ul>
          <li>Dapat menggunakan Zoom sebagai ruang kegiatan.</li>
          <li>Bisa menggunakan Moodle jika disediakan.</li>
          <li>Pendaftaran dapat diaktifkan atau dimatikan admin.</li>
        </ul>
      </article>

      <article class="help-type-card green">
        <div class="type-head">
          <span>👨‍🏫</span>
          <h3>Terbimbing</h3>
        </div>

        <ul>
          <li>Peserta biasanya perlu mengisi pendaftaran.</li>
          <li>Admin mengecek dan menginput peserta ke Moodle.</li>
          <li>Link Moodle aktif setelah admin menyetujui peserta.</li>
        </ul>
      </article>

      <article class="help-type-card orange">
        <div class="type-head">
          <span>⚡</span>
          <h3>Mandiri</h3>
        </div>

        <ul>
          <li>Peserta dapat langsung mengakses link yang tersedia.</li>
          <li>Tidak perlu menunggu persetujuan admin jika link sudah aktif.</li>
          <li>Cocok untuk pembelajaran mandiri.</li>
        </ul>
      </article>

    </div>
  </section>

  {{-- KETENTUAN --}}
  <section class="container help-content-section">
    <div class="help-section-heading">
      <span class="section-badge">Ketentuan</span>

      <h2>Informasi Penting Peserta</h2>

      <p>
        Perhatikan poin berikut sebelum mendaftar atau mengikuti kegiatan.
      </p>
    </div>

    <div class="help-notice-grid">

      <div class="help-notice-card">
        <h3>Yang Perlu Diperhatikan</h3>

        <div class="help-check-list">
          <div>
            <span>✓</span>
            <p>Gunakan email aktif saat mengisi pendaftaran.</p>
          </div>

          <div>
            <span>✓</span>
            <p>Pastikan nama, NIP, NIK, dan instansi diisi dengan benar.</p>
          </div>

          <div>
            <span>✓</span>
            <p>Link Moodle terbimbing tidak langsung aktif sebelum disetujui admin.</p>
          </div>

          <div>
            <span>✓</span>
            <p>Jika kegiatan tidak perlu pendaftaran, peserta dapat langsung mengakses link yang tersedia.</p>
          </div>

          <div>
            <span>✓</span>
            <p>Gunakan halaman Kelas Saya untuk melihat kegiatan aktif dan riwayat.</p>
          </div>
        </div>
      </div>

      <div class="help-alert-card">
        <span class="alert-badge">Penting</span>

        <h3>Login Peserta</h3>

        <p>
          Jika akun peserta dibuat otomatis oleh sistem, gunakan email yang
          didaftarkan sebagai username. Password awal mengikuti ketentuan sistem
          atau arahan admin.
        </p>

        <div class="alert-box">
          <strong>Catatan</strong>
          <p>
            Setelah login, peserta dapat melihat kegiatan aktif, riwayat kegiatan,
            serta tombol akses Zoom atau Moodle jika sudah tersedia.
          </p>
        </div>
      </div>

    </div>
  </section>

  {{-- FAQ --}}
  <section class="container help-content-section" id="faq">
    <div class="help-section-heading">
      <span class="section-badge">FAQ</span>

      <h2>Pertanyaan yang Sering Ditanyakan</h2>

      <p>
        Beberapa jawaban cepat untuk kendala yang sering dialami peserta.
      </p>
    </div>

    <div class="help-faq-list">

      <details class="faq-item" open>
        <summary>Apa semua kegiatan wajib isi pendaftaran?</summary>
        <div class="faq-content">
          Tidak. Kegiatan hanya wajib daftar jika admin mengaktifkan fitur
          pendaftaran pada kegiatan tersebut.
        </div>
      </details>

      <details class="faq-item">
        <summary>Kenapa link Moodle belum bisa dibuka?</summary>
        <div class="faq-content">
          Untuk kegiatan terbimbing, admin harus menyetujui peserta dan menginput
          data peserta ke Moodle terlebih dahulu.
        </div>
      </details>

      <details class="faq-item">
        <summary>Apakah kegiatan mandiri perlu persetujuan admin?</summary>
        <div class="faq-content">
          Umumnya tidak. Jika link Zoom atau Moodle sudah tersedia, peserta bisa
          langsung mengaksesnya.
        </div>
      </details>

      <details class="faq-item">
        <summary>Saya sudah daftar, lalu harus ke mana?</summary>
        <div class="faq-content">
          Baca instruksi setelah submit pendaftaran, lalu login ke halaman
          Kelas Saya untuk melihat status kegiatan.
        </div>
      </details>

      <details class="faq-item">
        <summary>Di mana saya melihat kegiatan yang sudah diikuti?</summary>
        <div class="faq-content">
          Login sebagai peserta, lalu buka halaman Kelas Saya. Di sana tersedia
          kegiatan aktif dan riwayat kegiatan.
        </div>
      </details>

    </div>
  </section>

  {{-- CTA --}}
  <section class="container help-cta-section">
    <div class="help-cta-card">
      <div>
        <span class="section-badge">Mulai Sekarang</span>

        <h2>Akses kegiatan dan kelas kamu dengan mudah.</h2>

        <p>
          Buka dashboard untuk memilih kegiatan, atau masuk ke halaman Kelas Saya
          untuk melihat kegiatan yang sudah kamu ikuti.
        </p>
      </div>

      <div class="help-cta-actions">
        <a href="{{ route('dashboard') }}" class="help-main-btn">
          Buka Dashboard
        </a>

        <a href="{{ route('kelas.index') }}" class="help-white-btn">
          Kelas Saya
        </a>
      </div>
    </div>
  </section>

</section>

@endsection
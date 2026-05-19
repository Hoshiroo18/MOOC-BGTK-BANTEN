@extends('layouts.app')

@section('title', 'Pendaftaran Berhasil - MOOC BGTK Banten')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/pendaftaran.css') }}">
@endpush

@section('content')

<section class="pendaftaran-page">
  <div class="pendaftaran-hero">
    <div class="container pendaftaran-hero-inner">
      <span class="student-eyebrow">Pendaftaran Berhasil</span>
      <h1>Data kamu berhasil dikirim.</h1>
      <p>Berikut petunjuk untuk mengikuti kegiatan yang sudah kamu pilih.</p>
    </div>
  </div>

  <div class="container pendaftaran-container">
    <div class="success-card">
      <div class="success-icon">✓</div>

      <h2>{{ optional($pesertaKegiatan->kegiatan)->nama_kegiatan ?? 'Kegiatan MOOC' }}</h2>

      @if($pesertaKegiatan->status_pendaftaran === 'menunggu')
        <p>
          Pendaftaran kamu sudah masuk. Untuk pelatihan terbimbing,
          admin akan menginput data peserta ke Moodle terlebih dahulu.
        </p>

        <div class="instruction-box warning">
          <strong>Status: Menunggu Persetujuan Admin</strong>
          <span>
            Link course Moodle akan aktif di halaman Kelas Saya setelah admin
            menekan tombol Sudah di Inject.
          </span>
        </div>
      @else
        <p>
          Pendaftaran kamu sudah masuk. Silakan ikuti kegiatan sesuai informasi
          yang tersedia dari admin.
        </p>

        <div class="instruction-box success">
          <strong>Status: Terdaftar</strong>
          <span>
            Kamu bisa melihat kegiatan yang diikuti melalui halaman Kelas Saya setelah login.
          </span>
        </div>
      @endif

      <div class="login-info-box">
        <strong>Informasi Peserta</strong>
        <p>Berikut data yang tersimpan dari pendaftaran kamu.</p>

        <div class="account-hint">
          <span>Nama:</span>
          <strong>{{ optional($pesertaKegiatan->peserta)->nama ?? '-' }}</strong>
        </div>

        <div class="account-hint">
          <span>Email:</span>
          <strong>{{ optional($pesertaKegiatan->peserta)->email ?? '-' }}</strong>
        </div>

        <div class="account-hint">
          <span>Instansi:</span>
          <strong>{{ optional($pesertaKegiatan->peserta)->Instansi ?? '-' }}</strong>
        </div>

        <div class="account-hint">
          <span>Terdaftar pada:</span>
          <strong>{{ \Carbon\Carbon::parse($pesertaKegiatan->terdaftar_at)->format('d M Y, H:i') }}</strong>
        </div>
      </div>

      <div class="success-actions">
        <a href="{{ route('dashboard') }}" class="success-main-btn">
          Kembali ke Dashboard
        </a>

    <a href="{{ route('kegiatan.login.form', $pesertaKegiatan->kegiatan->slug) }}" class="success-outline-btn">
    Login
</a>
      </div>
    </div>
  </div>
</section>

@endsection

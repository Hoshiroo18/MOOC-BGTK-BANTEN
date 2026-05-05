@extends('layouts.app')

@section('title', 'Pendaftaran Berhasil - MOOC BGTK Banten')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/pendaftaran.css') }}">
@endpush

@section('content')

@php
  $kegiatan = $kelas->kegiatan;

  $isTerbimbing = $kegiatan
    && $kegiatan->jenis_kegiatan === 'pelatihan'
    && $kegiatan->jenis_pelatihan === 'terbimbing';
@endphp

<section class="pendaftaran-page">

  <div class="pendaftaran-hero">
    <div class="container pendaftaran-hero-inner">
      <span class="student-eyebrow">Pendaftaran Berhasil</span>

      <h1>Data kamu berhasil dikirim.</h1>

      <p>
        Berikut petunjuk untuk mengikuti kegiatan yang sudah kamu pilih.
      </p>
    </div>
  </div>

  <div class="container pendaftaran-container">
    <div class="success-card">
      <div class="success-icon">✓</div>

      <h2>{{ $kegiatan->nama_kegiatan ?? 'Kegiatan MOOC' }}</h2>

      @if($isTerbimbing)
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
        <strong>Akun Peserta</strong>

        <p>
          Jika email ini belum pernah terdaftar, akun peserta otomatis dibuat.
          Gunakan email yang kamu isi dan NIK sebagai password awal.
        </p>

        <div class="account-hint">
          <span>Email login:</span>
          <strong>{{ $kelas->email }}</strong>
        </div>

        <div class="account-hint">
          <span>Password awal:</span>
          <strong>NIK yang kamu isi saat pendaftaran</strong>
        </div>
      </div>

      <div class="success-actions">
        <a href="{{ route('dashboard') }}" class="success-main-btn">
          Kembali ke Dashboard
        </a>

        <a href="{{ route('login') }}" class="success-outline-btn">
          Login Peserta
        </a>
      </div>
    </div>
  </div>

</section>

@endsection
@extends('layouts.app')

@section('title', 'Login - MOOC BGTK Banten')
@section('body_class', 'login-body')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')

<section class="login-page">
  <div class="login-wrapper">

    <div class="login-visual">
      <div class="login-visual-content">
        <span class="eyebrow">Masuk Akun</span>

        <h2>Belajar lebih mudah bersama MOOC BGTK Banten.</h2>

        <p>
          Akses kelas, pantau progres pembelajaran, unduh sertifikat,
          dan ikuti materi pelatihan digital dalam satu portal.
        </p>

        <div class="baduy-quote">
          <span class="quote-line"></span>
          <p>
            Terinspirasi dari kearifan lokal Baduy — sederhana, kuat,
            dan penuh makna.
          </p>
        </div>

        <div class="login-benefits">
          <div>
            <strong>100+</strong>
            <span>Materi Belajar</span>
          </div>

          <div>
            <strong>24/7</strong>
            <span>Akses Online</span>
          </div>

          <div>
            <strong>Gratis</strong>
            <span>Untuk Peserta</span>
          </div>
        </div>
      </div>
    </div>

    <div class="login-card">
      <div class="login-header">
        <span class="login-kicker">Portal Pembelajaran</span>
        <h1>Selamat Datang</h1>
        <p>Masukkan email dan password untuk melanjutkan.</p>
      </div>

      @if ($errors->any())
        <div class="alert-error">
          {{ $errors->first() }}
        </div>
      @endif

      <form action="{{ route('login.process') }}" method="POST" class="login-form">
        @csrf

        <div class="form-group">
          <label for="email">Email</label>
          <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="contoh@email.com"
            autocomplete="email"
            required
          >
        </div>

        <div class="form-group">
          <label for="password">Password</label>

          <div class="password-field">
            <input
              type="password"
              id="password"
              name="password"
              placeholder="Masukkan password"
              autocomplete="current-password"
              required
            >

            <button
              type="button"
              class="password-toggle"
              id="togglePassword"
              aria-label="Lihat password"
            >
              <svg
                id="passwordIcon"
                xmlns="http://www.w3.org/2000/svg"
                width="22"
                height="22"
                viewBox="0 0 24 24"
                fill="none"
              >
                <path
                  d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
                <circle
                  cx="12"
                  cy="12"
                  r="3"
                  stroke="currentColor"
                  stroke-width="2"
                />
              </svg>
            </button>
          </div>
        </div>

        <div class="form-options">
          <label class="remember">
            <input type="checkbox" name="remember">
            <span>Ingat saya</span>
          </label>

          <a href="#">Lupa password?</a>
        </div>

        <button type="submit" class="login-submit">
          Masuk Sekarang
        </button>
      </form>

      <p class="login-register">
        Belum punya akun?
        <a href="#">Hubungi admin</a>
      </p>
    </div>

  </div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('password');
    const toggleButton = document.getElementById('togglePassword');
    const passwordIcon = document.getElementById('passwordIcon');

    if (!passwordInput || !toggleButton || !passwordIcon) {
      return;
    }

    const eyeIcon = `
      <path
        d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
      />
      <circle
        cx="12"
        cy="12"
        r="3"
        stroke="currentColor"
        stroke-width="2"
      />
    `;

    const eyeOffIcon = `
      <path
        d="M3 3l18 18"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
      />
      <path
        d="M10.7 5.1A10.8 10.8 0 0 1 12 5c6.5 0 10 7 10 7a18.6 18.6 0 0 1-2.4 3.4"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
      />
      <path
        d="M6.6 6.9C3.7 8.7 2 12 2 12s3.5 7 10 7a9.7 9.7 0 0 0 4.1-.9"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
      />
      <path
        d="M9.9 9.9A3 3 0 0 0 14.1 14.1"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
      />
    `;

    toggleButton.addEventListener('click', function () {
      const isHidden = passwordInput.type === 'password';

      passwordInput.type = isHidden ? 'text' : 'password';
      passwordIcon.innerHTML = isHidden ? eyeOffIcon : eyeIcon;

      toggleButton.setAttribute(
        'aria-label',
        isHidden ? 'Sembunyikan password' : 'Lihat password'
      );

      passwordInput.focus();
    });
  });
</script>

@endsection

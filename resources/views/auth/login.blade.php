@extends('layouts.app')

@section('title', 'Login - MOOC BGTK Banten')

@section('content')

<section class="login-page">
  <div class="container login-wrapper">

    <div class="login-visual">
      <span class="eyebrow">Masuk Akun</span>
      <h2>Belajar lebih mudah bersama MOOC BGTK Banten.</h2>
      <p>
        Masuk untuk melanjutkan kelas, melihat progres pembelajaran,
        mengunduh sertifikat, dan mengakses materi pelatihan digital.
      </p>

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

    <div class="login-card">
<div class="login-header">
  <h1>Login</h1>
  <p>Masukkan email dan password kamu.</p>
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
            required
            >

        <div class="form-group">
          <label for="password">Password</label>
          <input 
            type="password" 
            id="password" 
            name="password" 
            placeholder="Masukkan password"
            required
          >
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

@endsection
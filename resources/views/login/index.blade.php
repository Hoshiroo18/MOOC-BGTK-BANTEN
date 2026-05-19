@extends('layouts.app')

@section('title', 'Login - ' . $kegiatan->nama_kegiatan)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
<style>
    .login-kegiatan-page {
        position: relative;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        background:
            radial-gradient(circle at 12% 12%, rgba(13, 86, 182, 0.13), transparent 30%),
            radial-gradient(circle at 90% 88%, rgba(10, 47, 107, 0.10), transparent 30%),
            linear-gradient(180deg, #ffffff 0%, #eef5ff 100%);
    }

    .login-kegiatan-page::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(rgba(255, 255, 255, 0.86), rgba(255, 255, 255, 0.86)),
            url("/images/baduy.jpg");
        background-size: 320px auto;
        background-repeat: repeat;
        background-position: center;
        opacity: 0.14;
        pointer-events: none;
    }

    .login-wrapper {
        position: relative;
        z-index: 2;
        width: min(1280px, calc(100% - 32px));
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1.25fr 0.8fr;
        gap: 34px;
        align-items: center;
    }

    /* LEFT PANEL */
    .login-visual {
        position: relative;
        width: 100%;
        min-height: 470px;
        padding: 30px;
        display: flex;
        align-items: flex-end;
        overflow: hidden;
        border-radius: 24px;
        background:
            linear-gradient(135deg, rgba(6, 31, 79, 0.95), rgba(13, 86, 182, 0.78)),
            url("/images/baduy.jpg");
        background-size: cover;
        background-position: center;
        box-shadow: 0 24px 64px rgba(10, 47, 107, 0.20);
    }

    .login-visual-content {
        position: relative;
        z-index: 3;
        max-width: 620px;
    }

    .login-visual .eyebrow {
        display: inline-flex;
        padding: 8px 14px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.16);
        border: 1px solid rgba(255, 255, 255, 0.18);
        color: #ffffff;
        font-size: 12px;
        font-weight: 900;
    }

    .login-visual h2 {
        margin: 18px 0 14px;
        color: #ffffff;
        font-size: 32px;
        line-height: 1.1;
        font-weight: 900;
    }

    .login-visual-content > p {
        color: rgba(255, 255, 255, 0.88);
        font-size: 14px;
        line-height: 1.6;
    }

    .baduy-quote {
        margin-top: 20px;
        padding: 14px 16px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(12px);
    }

    .quote-line {
        display: block;
        width: 48px;
        height: 4px;
        margin-bottom: 10px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.94);
    }

    .baduy-quote p {
        margin: 0;
        color: rgba(255, 255, 255, 0.92);
        font-size: 13px;
    }

    .login-benefits {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-top: 22px;
    }

    .login-benefits div {
        padding: 14px 16px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.14);
    }

    .login-benefits strong {
        display: block;
        margin-bottom: 6px;
        color: #ffffff;
        font-size: 24px;
        font-weight: 900;
    }

    .login-benefits span {
        color: rgba(255, 255, 255, 0.86);
        font-size: 12px;
        font-weight: 800;
    }

    /* RIGHT PANEL - LOGIN CARD */
    .login-card {
        width: 100%;
        max-width: 470px;
        justify-self: center;
        padding: 28px 32px;
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.96);
        box-shadow: 0 44px 110px rgba(10, 47, 107, 0.26);
    }

    .login-header {
        margin-bottom: 22px;
    }

    .login-kicker {
        display: inline-flex;
        margin-bottom: 12px;
        padding: 7px 13px;
        border-radius: 999px;
        background: #eaf2ff;
        color: #0d56b6;
        font-size: 11px;
        font-weight: 900;
    }

    .login-header h1 {
        margin: 0;
        color: #0a2f6b;
        font-size: 30px;
        font-weight: 900;
    }

    .login-header p {
        margin: 8px 0 0;
        color: #667085;
        font-size: 13px;
    }

    .alert-error {
        margin: 0 0 16px;
        padding: 12px 14px;
        border-radius: 14px;
        background: #fff0f0;
        color: #b42318;
        border: 1px solid #ffd0d0;
        font-size: 13px;
    }

    .login-form {
        display: grid;
        gap: 14px;
    }

    .form-group {
        display: grid;
        gap: 7px;
    }

    .form-group label {
        color: #0a2f6b;
        font-size: 13px;
        font-weight: 900;
    }

    .form-group input {
        width: 100%;
        height: 48px;
        border: 1px solid #dfe8f6;
        border-radius: 15px;
        padding: 0 15px;
        background: #eef5ff;
        font-size: 13px;
        outline: none;
    }

    .form-group input:focus {
        border-color: #0d56b6;
        background: #ffffff;
    }

    .login-submit {
        width: 100%;
        height: 48px;
        border: none;
        border-radius: 15px;
        background-color: #074a9a;
        background-image: linear-gradient(135deg, rgba(13, 86, 182, 0.66), rgba(13, 86, 182, 0.66)), url("/images/baduy.jpg");
        background-size: cover, 150px auto;
        color: #ffffff;
        font-size: 14px;
        font-weight: 900;
        cursor: pointer;
    }

    .login-submit:hover {
        transform: translateY(-2px);
    }

    .login-register {
        margin: 16px 0 0;
        text-align: center;
        font-size: 13px;
    }

    .login-register a {
        color: #0d56b6;
        text-decoration: none;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .login-visual {
            display: none !important;
        }
        .login-wrapper {
            grid-template-columns: 1fr;
        }
        .login-card {
            max-width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="login-kegiatan-page">
    <div class="login-wrapper">
        <!-- LEFT PANEL -->
        <div class="login-visual">
            <div class="login-visual-content">
                <span class="eyebrow">Akses Kegiatan</span>
                <h2>{{ $kegiatan->nama_kegiatan }}</h2>
                <p>Masukkan NIP dan Token untuk mengakses kegiatan ini.</p>
                <div class="baduy-quote">
                    <span class="quote-line"></span>
                    <p>"Belajar sepanjang hayat, menginspirasi dengan kearifan lokal Baduy."</p>
                </div>
                <div class="login-benefits">
                    <div>
                        <strong>{{ $kegiatan->kuota ?? '-' }}</strong>
                        <span>Kuota Peserta</span>
                    </div>
                    <div>
                        <strong>{{ optional($kegiatan->tipeKegiatan)->nama_kegiatan ?? '-' }}</strong>
                        <span>Tipe Kegiatan</span>
                    </div>
                    <div>
                        <strong>{{ optional($kegiatan->moda)->jenis_moda ?? '-' }}</strong>
                        <span>Moda</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="login-card">
            <div class="login-header">
                <span class="login-kicker">Login Peserta</span>
                <h1>Masuk ke Kegiatan</h1>
                <p>Masukkan NIP dan Token untuk mengakses kegiatan.</p>
            </div>

            @if($errors->any())
                <div class="alert-error">{{ $errors->first() }}</div>
            @endif

            @if(!$kegiatan->token_kegiatan)
                <div class="alert-error" style="background:#fffbeb; border-left-color:#f59e0b; color:#92400e;">
                    <strong>⚠️ Perhatian:</strong> Kegiatan ini belum memiliki token.
                </div>
            @endif

            <form action="{{ route('kegiatan.login', $kegiatan->slug) }}" method="POST" class="login-form">
                @csrf
                <div class="form-group">
                    <label for="nip">NIP</label>
                    <input type="text" id="nip" name="nip" value="{{ old('nip') }}" placeholder="Masukkan NIP (18 digit)" maxlength="18" required autofocus>
                </div>
                <div class="form-group">
                    <label for="token">Token Kegiatan</label>
                    <input type="text" id="token" name="token" placeholder="Masukkan token kegiatan" maxlength="10" required>
                </div>
                <button type="submit" class="login-submit">Login ke Kegiatan</button>
            </form>

            <p class="login-register">
                <a href="{{ route('dashboard') }}">← Kembali ke Beranda</a>
            </p>
        </div>
    </div>
</div>
@endsection

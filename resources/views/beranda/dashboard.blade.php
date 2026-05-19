@extends('layouts.app')

@section('title', 'Dashboard - ' . $kegiatan->nama_kegiatan)

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/kelas.css') }}">
@endpush

@section('content')

@php
  $pesertaKegiatan = \App\Models\PesertaKegiatan::where('peserta_id', $peserta->peserta_id)
    ->where('kegiatan_id', $kegiatan->kegiatan_id)
    ->first();
  $statusPeserta = $pesertaKegiatan->status ?? 'menunggu';
  $isTerbimbing = strtolower(optional($kegiatan->jenisKegiatan)->jenis_kegiatan ?? '') === 'terbimbing';
  $moodleUrl = $kegiatan->link_lms ?? '';
  $moodleAktif = false;
  if ($moodleUrl) {
    if ($isTerbimbing) {
      $moodleAktif = $statusPeserta === 'disetujui';
    } else {
      $moodleAktif = true;
    }
  }
@endphp

<section class="kelas-page">

  {{-- HERO --}}
  <section class="kelas-hero">
    <div class="container kelas-hero-inner">
      <span class="kelas-eyebrow">Dashboard Kegiatan</span>
      <h1>Selamat Datang, {{ $peserta->nama }}!</h1>
      <p>Anda telah login ke kegiatan <strong>{{ $kegiatan->nama_kegiatan }}</strong></p>
      <div class="kelas-hero-stats">
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
  </section>

  {{-- MAIN --}}
  <section class="container kelas-main-container">
    <div class="kelas-page-card">

   {{-- DATA PESERTA --}}
<section class="kelas-section">
  <div class="kelas-section-heading">
    <span class="kelas-section-kicker">Profil</span>
    <h2>Data Peserta</h2>
    <p>Informasi data diri yang terdaftar di kegiatan ini.</p>
  </div>

  <div class="kelas-list">
    <article class="kelas-card" style="grid-template-columns: 1fr;">
      <div class="kelas-card-body">
        <div class="kelas-badges">
          @if($isTerbimbing)
            @if($statusPeserta === 'disetujui')
              <span class="badge-success">Disetujui</span>
            @else
              <span class="badge-warning">Menunggu Persetujuan</span>
            @endif
          @endif
        </div>
        <h3>{{ $peserta->nama }}</h3>
        <div class="kelas-meta">
          <small>NIP: {{ $peserta->nip ?? '-' }}</small>
          <small>NIK: {{ $peserta->nik ?? '-' }}</small>
          <small>Email: {{ $peserta->email }}</small>
          <small>Jenis Kelamin: {{ $peserta->jenis_kelamin ?? '-' }}</small>
          <small>Tanggal Lahir: {{ $peserta->tanggal_lahir ? \Carbon\Carbon::parse($peserta->tanggal_lahir)->format('d M Y') : '-' }}</small>
          <small>Instansi: {{ $peserta->Instansi ?? '-' }}</small>
        </div>
      </div>
    </article>
  </div>
</section>

{{-- INFO KEGIATAN --}}
<section class="kelas-section" style="margin-top:24px;">
  <div class="kelas-section-heading">
    <span class="kelas-section-kicker">Informasi</span>
    <h2>Detail Kegiatan</h2>
    <p>Informasi lengkap tentang kegiatan yang sedang diikuti.</p>
  </div>

  <div class="kelas-list">
    <article class="kelas-card">
      <div class="kelas-flayer-wrap">
        <img
          src="{{ $kegiatan->flayer ? asset('storage/' . $kegiatan->flayer) : asset('images/baduy.jpg') }}"
          alt="{{ $kegiatan->nama_kegiatan }}"
          class="kelas-flayer"
        >
      </div>
      <div class="kelas-card-body">
        <div class="kelas-badges">
          <span>{{ optional($kegiatan->tipeKegiatan)->nama_kegiatan ?? '-' }}</span>
          <span>{{ optional($kegiatan->moda)->jenis_moda ?? '-' }}</span>
          @if(optional($kegiatan->jenisKegiatan)->jenis_kegiatan)
            <span>{{ $kegiatan->jenisKegiatan->jenis_kegiatan }}</span>
          @endif
        </div>
        <h3>{{ $kegiatan->nama_kegiatan }}</h3>
        <p>{{ \Illuminate\Support\Str::limit(strip_tags($kegiatan->deskripsi), 300) }}</p>
        <div class="kelas-meta">
          <small>Fasilitator: {{ $kegiatan->fasilitators->pluck('nama')->join(', ') ?: '-' }}</small>
          <small>Kuota: {{ $kegiatan->kuota ?? '-' }}</small>
          <small>
            Waktu:
            {{ $kegiatan->start_date ? \Carbon\Carbon::parse($kegiatan->start_date)->format('d M Y') : '-' }}
            s/d
            {{ $kegiatan->end_date ? \Carbon\Carbon::parse($kegiatan->end_date)->format('d M Y') : '-' }}
          </small>
        </div>
        <div class="kelas-actions">
          @if($kegiatan->link_zoom)
            <a href="{{ $kegiatan->link_zoom }}" target="_blank" rel="noopener" class="kelas-secondary-btn">
              🎥 Buka Zoom
            </a>
          @endif

          @if($moodleAktif && $moodleUrl)
            <a href="{{ $moodleUrl }}" target="_blank" rel="noopener" class="kelas-main-btn">
              📚 Masuk Course Moodle
            </a>
          @elseif($moodleUrl)
            <button type="button" class="kelas-main-btn is-disabled" disabled>
              Menunggu Persetujuan Admin
            </button>
          @else
            <button type="button" class="kelas-main-btn is-disabled" disabled>
              Moodle Belum Tersedia
            </button>
          @endif
        </div>
      </div>
    </article>
  </div>
</section>

      {{-- LOGOUT --}}
      {{-- <div style="margin-top:24px;text-align:center;">
        <form action="{{ route('kegiatan.logout', ['slug' => $kegiatan->slug]) }}" method="POST" style="display:inline;">
          @csrf
          <button type="submit" class="kelas-reset-btn" style="padding:0 28px;min-height:46px;font-size:14px;">
            🚪 Logout dari Kegiatan
          </button>
        </form>
      </div> --}}

    </div>
  </section>
</section>

@endsection

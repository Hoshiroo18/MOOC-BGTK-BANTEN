@extends('layouts.app')

@section('title', 'Kelas Saya - MOOC BGTK Banten')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/kelas.css') }}">
@endpush

@section('content')

@php
  $kelasAktif = collect($kelasAktif ?? []);
  $riwayatKelas = collect($riwayatKelas ?? []);
  $kegiatanUser = session('auth_peserta');
  $isKegiatanLogin = !empty($kegiatanUser);

  $makeKelasCard = function ($kelasItem) {
    $kegiatan = null;
    $kelasId = null;
    $namaPeserta = null;
    $aslInstansi = null;

    if ($kelasItem instanceof \App\Models\PesertaKegiatan) {
      $kegiatan = $kelasItem->kegiatan;
      $kelasId = $kelasItem->peserta_kegiatan_id;
      $namaPeserta = $kelasItem->peserta->nama ?? '-';
      $aslInstansi = $kelasItem->peserta->Instansi ?? '-';
    } else {
      $kegiatan = $kelasItem->kegiatan;
      $kelasId = $kelasItem->id;
      $namaPeserta = $kelasItem->nama ?? auth()->user()->name ?? '-';
      $aslInstansi = $kelasItem->asal_instansi ?? $kelasItem->instansi ?? '-';
    }

    if (!$kegiatan) {
      return null;
    }

    $jenisRaw = strtolower(optional($kegiatan->tipeKegiatan)->nama_kegiatan ?? 'kegiatan');
    $modaRaw = strtolower(optional($kegiatan->moda)->jenis_moda ?? '-');
    $jenisPelatihanRaw = strtolower(optional($kegiatan->jenisKegiatan)->jenis_kegiatan ?? '');

    $waktuText = '-';
    if (!empty($kegiatan->start_date) || !empty($kegiatan->end_date)) {
      $start = $kegiatan->start_date ? \Carbon\Carbon::parse($kegiatan->start_date)->format('d M Y') : '-';
      $end   = $kegiatan->end_date   ? \Carbon\Carbon::parse($kegiatan->end_date)->format('d M Y')   : '-';
      $waktuText = $start . ' s/d ' . $end;
    }

    $flayerUrl = !empty($kegiatan->flayer)
      ? asset('storage/' . $kegiatan->flayer)
      : asset('images/baduy.jpg');

    $moodleUrl = $kegiatan->link_lms ?? '';
    $isTerbimbing = $jenisPelatihanRaw === 'terbimbing';
    $moodleAktif = false;

    if ($moodleUrl) {
    if ($isTerbimbing) {
        // Cek status di peserta_kegiatan
        $statusPeserta = $kelasItem->status ?? $kelasItem->pivot->status ?? null;
        $moodleAktif = $statusPeserta === 'disetujui';
    } else {
        $moodleAktif = true;
    }
    }

    if (!$moodleUrl) {
      $moodleStatus = 'Moodle Belum Tersedia';
      $moodleStatusClass = 'badge-muted';
    } elseif ($moodleAktif) {
      $moodleStatus = 'Moodle Aktif';
      $moodleStatusClass = 'badge-success';
    } else {
      $moodleStatus = 'Menunggu Admin';
      $moodleStatusClass = 'badge-warning';
    }

    return [
      'id'                  => $kelasId,
      'title'               => $kegiatan->nama_kegiatan ?? 'Kegiatan MOOC',
      'title_lower'         => strtolower($kegiatan->nama_kegiatan ?? 'kegiatan mooc'),
      'jenis_raw'           => $jenisRaw,
      'jenis'               => ucfirst($jenisRaw),
      'moda_raw'            => $modaRaw,
      'moda'                => ucfirst($modaRaw),
      'jenis_pelatihan_raw' => $jenisPelatihanRaw,
      'jenis_pelatihan'     => $jenisPelatihanRaw ? ucfirst($jenisPelatihanRaw) : '',
      'deskripsi'           => strip_tags($kegiatan->deskripsi ?? ''),
      'deskripsi_short'     => \Illuminate\Support\Str::limit(strip_tags($kegiatan->deskripsi ?? ''), 150),
      'flayer'              => $flayerUrl,
      'waktu'               => $waktuText,
      'nama_peserta'        => $namaPeserta,
      'instansi'            => $aslInstansi,
      'link_zoom'           => $kegiatan->link_zoom ?? '',
      'moodle_url'          => $moodleUrl,
      'moodle_aktif'        => $moodleAktif,
      'moodle_status'       => $moodleStatus,
      'moodle_status_class' => $moodleStatusClass,
    ];
  };
@endphp

<section class="kelas-page">

  {{-- HERO --}}
  <section class="kelas-hero">
    <div class="container kelas-hero-inner">
      <span class="kelas-eyebrow">Kelas Saya</span>
      <h1>Kegiatan yang Kamu Ikuti</h1>
      <p>
        Halaman ini berisi kegiatan yang sudah kamu daftar. Link Moodle untuk
        kegiatan terbimbing akan aktif setelah admin menyetujui.
      </p>
      <div class="kelas-hero-stats">
        <div>
          <strong>{{ $totalKelas ?? 0 }}</strong>
          <span>Total Kegiatan</span>
        </div>
        <div>
          <strong>{{ $totalAktif ?? 0 }}</strong>
          <span>Sedang Diikuti</span>
        </div>
        <div>
          <strong>{{ $totalRiwayat ?? 0 }}</strong>
          <span>Riwayat</span>
        </div>
      </div>
    </div>
  </section>

  {{-- MAIN --}}
  <section class="container kelas-main-container">
    <div class="kelas-page-card">

      {{-- TOOLBAR (tampil untuk semua, filter JS hanya aktif saat kegiatan login) --}}
      <div class="kelas-toolbar">
        <input
          type="text"
          id="kelasSearchInput"
          placeholder="Cari nama kegiatan..."
          autocomplete="off"
        >
        <div class="kelas-filter-buttons">
          <button type="button" class="kelas-filter-btn is-active" data-jenis="">Semua</button>
          <button type="button" class="kelas-filter-btn" data-jenis="konsultasi">Konsultasi</button>
          <button type="button" class="kelas-filter-btn" data-jenis="webinar">Webinar</button>
          <button type="button" class="kelas-filter-btn" data-jenis="pelatihan">Pelatihan</button>
        </div>
        <button type="button" id="resetKelasFilter" class="kelas-reset-btn">Reset</button>
      </div>

      {{-- AKTIF --}}
      <section class="kelas-section">
        <div class="kelas-section-heading">
          <span class="kelas-section-kicker">Aktif</span>
          <h2>Kegiatan yang Sedang atau Akan Diikuti</h2>
          <p>Kegiatan yang belum melewati waktu pelaksanaan.</p>
        </div>

        <div class="kelas-empty-filter" id="kelasAktifEmpty" hidden>
          Kegiatan aktif tidak ditemukan.
        </div>

        <div class="kelas-list" id="kelasAktifList">
          @forelse($kelasAktif as $kelasItem)
            @php $card = $makeKelasCard($kelasItem); @endphp
            @if($card)
              <article
                class="kelas-card js-kelas-card"
                data-section="aktif"
                data-title="{{ $card['title_lower'] }}"
                data-jenis="{{ $card['jenis_raw'] }}"
              >
                <div class="kelas-flayer-wrap">
                  <img src="{{ $card['flayer'] }}" alt="{{ $card['title'] }}" class="kelas-flayer">
                </div>
                <div class="kelas-card-body">
                  <div class="kelas-badges">
                    <span>{{ $card['jenis'] }}</span>
                    <span>{{ $card['moda'] }}</span>
                    @if($card['jenis_pelatihan'])
                      <span>{{ $card['jenis_pelatihan'] }}</span>
                    @endif
                    <span class="{{ $card['moodle_status_class'] }}">{{ $card['moodle_status'] }}</span>
                  </div>
                  <h3>{{ $card['title'] }}</h3>
                  <p>{{ $card['deskripsi_short'] ?: 'Detail kegiatan akan segera diperbarui.' }}</p>
                  <div class="kelas-meta">
                    <small>Peserta: {{ $card['nama_peserta'] }}</small>
                    <small>Instansi: {{ $card['instansi'] }}</small>
                    <small>Waktu: {{ $card['waktu'] }}</small>
                  </div>
                  <div class="kelas-actions">
                    @if($card['link_zoom'])
                      <a href="{{ $card['link_zoom'] }}" target="_blank" rel="noopener" class="kelas-secondary-btn">
                        Buka Zoom
                      </a>
                    @endif
                    @if($card['moodle_aktif'] && $card['moodle_url'])
                      <a href="{{ $card['moodle_url'] }}" target="_blank" rel="noopener" class="kelas-main-btn">
                        Masuk Course Moodle
                      </a>
                    @elseif($card['moodle_url'])
                      <button type="button" class="kelas-main-btn is-disabled" disabled>Menunggu Admin</button>
                    @else
                      <button type="button" class="kelas-main-btn is-disabled" disabled>Moodle Belum Tersedia</button>
                    @endif
                  </div>
                </div>
              </article>
            @endif
          @empty
            <div class="kelas-empty-state">
              <h3>Belum ada kegiatan aktif.</h3>
              <p>Kegiatan yang kamu daftar dan belum selesai akan muncul di sini.</p>
            </div>
          @endforelse
        </div>
      </section>

      {{-- RIWAYAT --}}
      <section class="kelas-section riwayat-section">
        <div class="kelas-section-heading">
          <span class="kelas-section-kicker">Riwayat</span>
          <h2>Riwayat Kegiatan yang Pernah Diikuti</h2>
          <p>Kegiatan yang waktu pelaksanaannya sudah lewat akan tersimpan di sini.</p>
        </div>

        <div class="kelas-empty-filter" id="kelasRiwayatEmpty" hidden>
          Riwayat kegiatan tidak ditemukan.
        </div>

        <div class="kelas-list" id="kelasRiwayatList">
          @forelse($riwayatKelas as $kelasItem)
            @php $card = $makeKelasCard($kelasItem); @endphp
            @if($card)
              <article
                class="kelas-card kelas-history-card js-kelas-card"
                data-section="riwayat"
                data-title="{{ $card['title_lower'] }}"
                data-jenis="{{ $card['jenis_raw'] }}"
              >
                <div class="kelas-flayer-wrap">
                  <img src="{{ $card['flayer'] }}" alt="{{ $card['title'] }}" class="kelas-flayer">
                </div>
                <div class="kelas-card-body">
                  <div class="kelas-badges">
                    <span>{{ $card['jenis'] }}</span>
                    <span>{{ $card['moda'] }}</span>
                    @if($card['jenis_pelatihan'])
                      <span>{{ $card['jenis_pelatihan'] }}</span>
                    @endif
                    <span class="badge-history">Selesai</span>
                  </div>
                  <h3>{{ $card['title'] }}</h3>
                  <p>{{ $card['deskripsi_short'] ?: 'Detail kegiatan akan segera diperbarui.' }}</p>
                  <div class="kelas-meta">
                    <small>Peserta: {{ $card['nama_peserta'] }}</small>
                    <small>Instansi: {{ $card['instansi'] }}</small>
                    <small>Waktu: {{ $card['waktu'] }}</small>
                  </div>
                  <div class="kelas-actions">
                    @if($card['moodle_aktif'] && $card['moodle_url'])
                      <a href="{{ $card['moodle_url'] }}" target="_blank" rel="noopener" class="kelas-main-btn">
                        Buka Kembali Moodle
                      </a>
                    @elseif($card['link_zoom'])
                      <a href="{{ $card['link_zoom'] }}" target="_blank" rel="noopener" class="kelas-secondary-btn">
                        Buka Zoom
                      </a>
                    @else
                      <button type="button" class="kelas-main-btn is-disabled" disabled>Link Tidak Tersedia</button>
                    @endif
                  </div>
                </div>
              </article>
            @endif
          @empty
            <div class="kelas-empty-state">
              <h3>Belum ada riwayat kegiatan.</h3>
              <p>Kegiatan yang sudah lewat akan muncul sebagai riwayat.</p>
            </div>
          @endforelse
        </div>
      </section>

    </div>
  </section>
</section>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const isKegiatanLogin = {{ $isKegiatanLogin ? 'true' : 'false' }};

    const searchInput  = document.getElementById('kelasSearchInput');
    const filterButtons = document.querySelectorAll('.kelas-filter-btn');
    const resetButton  = document.getElementById('resetKelasFilter');
    const cards        = document.querySelectorAll('.js-kelas-card');
    const aktifEmpty   = document.getElementById('kelasAktifEmpty');
    const riwayatEmpty = document.getElementById('kelasRiwayatEmpty');

    let selectedJenis = '';

    function countSectionCards(section) {
      return document.querySelectorAll('.js-kelas-card[data-section="' + section + '"]').length;
    }

    function filterKelas() {
      const keyword = searchInput ? searchInput.value.toLowerCase().trim() : '';
      let aktifVisible = 0;
      let riwayatVisible = 0;

      cards.forEach(function (card) {
        const title   = card.dataset.title || '';
        const jenis   = card.dataset.jenis || '';
        const section = card.dataset.section || '';

        const matchKeyword = title.includes(keyword);
        const matchJenis   = selectedJenis === '' || jenis === selectedJenis;

        if (matchKeyword && matchJenis) {
          card.style.display = '';
          if (section === 'aktif')    aktifVisible++;
          if (section === 'riwayat') riwayatVisible++;
        } else {
          card.style.display = 'none';
        }
      });

      if (aktifEmpty)   aktifEmpty.hidden   = aktifVisible > 0   || countSectionCards('aktif') === 0;
      if (riwayatEmpty) riwayatEmpty.hidden = riwayatVisible > 0 || countSectionCards('riwayat') === 0;
    }

    // Search hanya aktif untuk kegiatan login
    if (isKegiatanLogin && searchInput) {
      searchInput.addEventListener('input', filterKelas);
    }

    // Filter jenis hanya aktif untuk kegiatan login
    if (isKegiatanLogin) {
      filterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
          selectedJenis = button.dataset.jenis || '';
          filterButtons.forEach(function (btn) { btn.classList.remove('is-active'); });
          button.classList.add('is-active');
          filterKelas();
        });
      });
    }

    // Reset hanya aktif untuk kegiatan login
    if (isKegiatanLogin && resetButton) {
      resetButton.addEventListener('click', function () {
        selectedJenis = '';
        if (searchInput) searchInput.value = '';
        filterButtons.forEach(function (btn) {
          btn.classList.remove('is-active');
          if ((btn.dataset.jenis || '') === '') btn.classList.add('is-active');
        });
        filterKelas();
      });
    }
  });
</script>

@endsection

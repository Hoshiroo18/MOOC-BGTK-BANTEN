@extends('layouts.app')

@section('title', 'Dashboard - MOOC BGTK Banten')

@section('content')

@php
  $kegiatanSource = $kegiatan ?? collect();

  $kegiatanCollection = method_exists($kegiatanSource, 'getCollection')
    ? $kegiatanSource->getCollection()
    : collect($kegiatanSource);

  $totalKegiatan = $kegiatanCollection->count();
  $totalWebinar = $kegiatanCollection->where('jenis_kegiatan', 'webinar')->count();
  $totalPelatihan = $kegiatanCollection->where('jenis_kegiatan', 'pelatihan')->count();
  $totalKonsultasi = $kegiatanCollection->where('jenis_kegiatan', 'konsultasi')->count();

  $kegiatanCards = $kegiatanCollection->values()->map(function ($item) {
    $jenisRaw = strtolower($item->jenis_kegiatan ?? 'kegiatan');
    $modaRaw = strtolower($item->moda ?? '-');

    $waktuText = '-';

    if (!empty($item->waktu_pelaksanaan)) {
      $waktuText = \Carbon\Carbon::parse($item->waktu_pelaksanaan)->format('d M Y, H:i');
    }

    $flayerUrl = !empty($item->flayer)
      ? asset('storage/' . $item->flayer)
      : asset('images/baduy.jpg');

    /*
      Field lokasi dan Moodle ini aman walaupun belum ada di tabel.
      Kalau nanti admin form ditambah kolom lokasi/link_moodle, otomatis kebaca.
    */
    $lokasi = $item->lokasi
      ?? $item->tempat_kegiatan
      ?? $item->alamat_kegiatan
      ?? '';

    $moodleLink = $item->link_moodle
      ?? $item->moodle_url
      ?? $item->course_url
      ?? '';

    $courseName = $item->nama_course
      ?? $item->course_moodle
      ?? 'Course Moodle';

    return [
      'id' => $item->id,
      'title' => $item->nama_kegiatan ?? 'Kegiatan MOOC',
      'jenis_raw' => $jenisRaw,
      'jenis' => ucfirst($jenisRaw),
      'moda_raw' => $modaRaw,
      'moda' => ucfirst($modaRaw),
      'deskripsi' => strip_tags($item->deskripsi ?? ''),
      'deskripsi_short' => \Illuminate\Support\Str::limit(strip_tags($item->deskripsi ?? ''), 125),
      'fasil' => $item->fasil ?? '-',
      'kuota' => $item->kuota ?? '-',
      'waktu' => $waktuText,
      'flayer' => $flayerUrl,
      'link_pendaftaran' => $item->link_pendaftaran ?? '',
      'link_zoom' => $item->link_zoom ?? '',
      'lokasi' => $lokasi,
      'moodle_link' => $moodleLink,
      'course_name' => $courseName,
    ];
  });
@endphp

<section class="public-dashboard-page">

  {{-- HERO --}}
  <section class="student-hero">
    <div class="container student-hero-grid">

      <div class="student-hero-copy">
        <span class="student-eyebrow">MOOC BGTK Banten</span>

        <h1>Temukan Kegiatan dan Course Pembelajaran yang Tersedia.</h1>

        <p>
          Pilih webinar, konsultasi, atau pelatihan yang sudah disediakan admin.
          Lihat detail kegiatan, isi link pendaftaran, lalu akses pembelajaran
          melalui Zoom, lokasi kegiatan, atau course Moodle.
        </p>

        <div class="student-hero-actions">
          <a href="#daftar-kegiatan" class="student-main-btn">
            Lihat Kegiatan
          </a>

          <a href="#alur-belajar" class="student-outline-btn">
            Cara Mengikuti
          </a>
        </div>

        <div class="student-stats">
          <div>
            <strong>{{ $totalKegiatan }}</strong>
            <span>Total Kegiatan</span>
          </div>

          <div>
            <strong>{{ $totalWebinar }}</strong>
            <span>Webinar</span>
          </div>

          <div>
            <strong>{{ $totalPelatihan }}</strong>
            <span>Pelatihan</span>
          </div>

          <div>
            <strong>{{ $totalKonsultasi }}</strong>
            <span>Konsultasi</span>
          </div>
        </div>
      </div>

      <div class="student-hero-panel">
        <div class="student-preview-card">
          <span class="preview-label">Terhubung Moodle</span>

          <h2>Daftar kegiatan, lalu lanjutkan pembelajaran secara online.</h2>

          <div class="preview-list">
            <div>
              <span class="preview-dot"></span>
              <p>Pilih kegiatan yang sesuai dengan kebutuhan belajar kamu.</p>
            </div>

            <div>
              <span class="preview-dot"></span>
              <p>Akses Zoom untuk daring, lokasi untuk luring, atau keduanya untuk hybrid.</p>
            </div>

            <div>
              <span class="preview-dot"></span>
              <p>Masuk ke course Moodle yang disiapkan guru atau fasilitator.</p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>

  {{-- ALUR --}}
  <section class="container learning-flow" id="alur-belajar">
    <div class="flow-card">
      <span>01</span>
      <h3>Pilih Kegiatan</h3>
      <p>Pilih webinar, konsultasi, atau pelatihan yang tersedia.</p>
    </div>

    <div class="flow-card">
      <span>02</span>
      <h3>Isi Pendaftaran</h3>
      <p>Klik detail kegiatan, lalu isi link pendaftaran yang disediakan.</p>
    </div>

    <div class="flow-card">
      <span>03</span>
      <h3>Akses Pembelajaran</h3>
      <p>Ikuti kegiatan melalui Zoom, lokasi luring, atau course Moodle.</p>
    </div>
  </section>

  {{-- DAFTAR KEGIATAN --}}
  <section class="container public-course-section" id="daftar-kegiatan">
    <div class="public-section-heading">
      <div>
        <span class="section-kicker">Daftar Kegiatan</span>

        <h2>Kegiatan dan Course yang Tersedia</h2>

        <p>
          Klik card kegiatan untuk melihat detail pelatihan, link pendaftaran,
          akses Zoom, lokasi kegiatan, dan course Moodle.
        </p>
      </div>
    </div>

    <div class="course-toolbar">
      <input
        type="text"
        id="courseSearch"
        placeholder="Cari kegiatan..."
        autocomplete="off"
      >

      <select id="jenisFilter">
        <option value="">Semua Jenis</option>
        <option value="webinar">Webinar</option>
        <option value="pelatihan">Pelatihan</option>
        <option value="konsultasi">Konsultasi</option>
      </select>

      <select id="modaFilter">
        <option value="">Semua Moda</option>
        <option value="daring">Daring</option>
        <option value="luring">Luring</option>
        <option value="hybrid">Hybrid</option>
      </select>

      <button type="button" id="resetCourseFilter">
        Reset
      </button>
    </div>

    <div class="course-empty-filter" id="courseEmptyFilter" hidden>
      Kegiatan tidak ditemukan.
    </div>

    <div class="public-course-grid" id="publicCourseGrid">
      @forelse($kegiatanCards as $card)
        <article
          class="public-course-card js-open-detail"
          tabindex="0"
          data-id="{{ $card['id'] }}"
          data-title="{{ strtolower($card['title']) }}"
          data-jenis="{{ $card['jenis_raw'] }}"
          data-moda="{{ $card['moda_raw'] }}"
        >
          <div class="course-image-wrap">
            <img
              src="{{ $card['flayer'] }}"
              alt="{{ $card['title'] }}"
              class="course-image"
            >

            <div class="course-image-overlay">
              <span>{{ $card['jenis'] }}</span>
              <span>{{ $card['moda'] }}</span>
            </div>
          </div>

          <div class="course-card-body">
            <div class="course-tags">
              <span>{{ $card['jenis'] }}</span>
              <span>{{ $card['moda'] }}</span>
            </div>

            <h3>{{ $card['title'] }}</h3>

            <p>
              {{ $card['deskripsi_short'] ?: 'Detail kegiatan akan segera diperbarui.' }}
            </p>

            <div class="course-meta">
              <small>Fasilitator: {{ $card['fasil'] }}</small>
              <small>Kuota: {{ $card['kuota'] }}</small>
              <small>Waktu: {{ $card['waktu'] }}</small>
            </div>

            <button type="button" class="course-detail-btn">
              Lihat Detail
            </button>
          </div>
        </article>
      @empty
        <div class="public-empty-state">
          <h3>Belum ada kegiatan tersedia.</h3>
          <p>
            Kegiatan yang dibuat admin akan muncul otomatis di halaman ini.
          </p>
        </div>
      @endforelse
    </div>
  </section>

  {{-- MODAL DETAIL --}}
  <div class="course-modal" id="courseDetailModal" hidden>
    <div class="course-modal-backdrop" data-close-modal></div>

    <div class="course-modal-card" role="dialog" aria-modal="true">
      <button type="button" class="course-modal-close" data-close-modal>
        ×
      </button>

      <div class="modal-image-wrap">
        <img src="" alt="" id="modalImage">
      </div>

      <div class="modal-content">
        <div class="modal-badges">
          <span id="modalJenis">Jenis</span>
          <span id="modalModa">Moda</span>
        </div>

        <h2 id="modalTitle">Judul Kegiatan</h2>

        <p id="modalDescription">
          Deskripsi kegiatan.
        </p>

        <div class="modal-info-grid">
          <div>
            <span>Fasilitator</span>
            <strong id="modalFasil">-</strong>
          </div>

          <div>
            <span>Kuota</span>
            <strong id="modalKuota">-</strong>
          </div>

          <div>
            <span>Waktu</span>
            <strong id="modalWaktu">-</strong>
          </div>
        </div>

        <div class="access-box">
          <h3>Akses Kegiatan</h3>

          <div class="access-item" id="zoomAccessBox" hidden>
            <div>
              <strong>Link Zoom</strong>
              <p>Untuk kegiatan moda daring atau hybrid.</p>
            </div>

            <a href="#" target="_blank" rel="noopener" id="modalZoomLink">
              Buka Zoom
            </a>
          </div>

          <div class="access-item" id="lokasiAccessBox" hidden>
            <div>
              <strong>Lokasi Kegiatan</strong>
              <p id="modalLokasi">Lokasi akan diinformasikan oleh admin.</p>
            </div>
          </div>

          <div class="access-item">
            <div>
              <strong>Course Moodle</strong>
              <p id="modalCourseName">Course Moodle yang terhubung dengan kegiatan ini.</p>
            </div>

            <a href="#" target="_blank" rel="noopener" id="modalMoodleLink">
              Buka Moodle
            </a>
          </div>
        </div>

        <div class="modal-actions">
          <a href="#" target="_blank" rel="noopener" class="register-btn" id="modalRegisterLink">
            Isi Pendaftaran
          </a>

          <button type="button" class="cancel-btn" data-close-modal>
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>

</section>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const kegiatanData = @json($kegiatanCards->values());

    const modal = document.getElementById('courseDetailModal');
    const modalImage = document.getElementById('modalImage');
    const modalJenis = document.getElementById('modalJenis');
    const modalModa = document.getElementById('modalModa');
    const modalTitle = document.getElementById('modalTitle');
    const modalDescription = document.getElementById('modalDescription');
    const modalFasil = document.getElementById('modalFasil');
    const modalKuota = document.getElementById('modalKuota');
    const modalWaktu = document.getElementById('modalWaktu');
    const modalLokasi = document.getElementById('modalLokasi');
    const modalCourseName = document.getElementById('modalCourseName');

    const zoomBox = document.getElementById('zoomAccessBox');
    const lokasiBox = document.getElementById('lokasiAccessBox');

    const modalZoomLink = document.getElementById('modalZoomLink');
    const modalMoodleLink = document.getElementById('modalMoodleLink');
    const modalRegisterLink = document.getElementById('modalRegisterLink');

    const cards = document.querySelectorAll('.js-open-detail');
    const closeButtons = document.querySelectorAll('[data-close-modal]');

    const searchInput = document.getElementById('courseSearch');
    const jenisFilter = document.getElementById('jenisFilter');
    const modaFilter = document.getElementById('modaFilter');
    const resetButton = document.getElementById('resetCourseFilter');
    const emptyFilter = document.getElementById('courseEmptyFilter');

    function getItemById(id) {
      return kegiatanData.find(function (item) {
        return String(item.id) === String(id);
      });
    }

    function setLink(element, url, activeText) {
      if (url && String(url).trim() !== '') {
        element.href = url;
        element.classList.remove('is-disabled');
        element.textContent = activeText;
      } else {
        element.href = '#';
        element.classList.add('is-disabled');
        element.textContent = 'Belum tersedia';
      }
    }

    function openModal(item) {
      if (!item || !modal) {
        return;
      }

      modalImage.src = item.flayer;
      modalImage.alt = item.title;

      modalJenis.textContent = item.jenis;
      modalModa.textContent = item.moda;
      modalTitle.textContent = item.title;
      modalDescription.textContent = item.deskripsi || 'Detail kegiatan akan segera diperbarui.';
      modalFasil.textContent = item.fasil || '-';
      modalKuota.textContent = item.kuota || '-';
      modalWaktu.textContent = item.waktu || '-';

      const moda = String(item.moda_raw || '').toLowerCase();

      zoomBox.hidden = !(moda === 'daring' || moda === 'hybrid');
      lokasiBox.hidden = !(moda === 'luring' || moda === 'hybrid');

      modalLokasi.textContent = item.lokasi || 'Lokasi akan diinformasikan oleh admin.';
      modalCourseName.textContent = item.course_name || 'Course Moodle';

      setLink(modalZoomLink, item.link_zoom, 'Buka Zoom');
      setLink(modalMoodleLink, item.moodle_link, 'Buka Moodle');
      setLink(modalRegisterLink, item.link_pendaftaran, 'Isi Pendaftaran');

      modal.hidden = false;
      document.body.classList.add('modal-open');
    }

    function closeModal() {
      modal.hidden = true;
      document.body.classList.remove('modal-open');
    }

    cards.forEach(function (card) {
      card.addEventListener('click', function () {
        openModal(getItemById(card.dataset.id));
      });

      card.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' || event.key === ' ') {
          event.preventDefault();
          openModal(getItemById(card.dataset.id));
        }
      });
    });

    closeButtons.forEach(function (button) {
      button.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && modal && !modal.hidden) {
        closeModal();
      }
    });

    function filterCourses() {
      const keyword = searchInput ? searchInput.value.toLowerCase().trim() : '';
      const jenisValue = jenisFilter ? jenisFilter.value : '';
      const modaValue = modaFilter ? modaFilter.value : '';

      let visibleCount = 0;

      cards.forEach(function (card) {
        const title = card.dataset.title || '';
        const jenis = card.dataset.jenis || '';
        const moda = card.dataset.moda || '';

        const matchKeyword = title.includes(keyword);
        const matchJenis = jenisValue === '' || jenis === jenisValue;
        const matchModa = modaValue === '' || moda === modaValue;

        if (matchKeyword && matchJenis && matchModa) {
          card.style.display = '';
          visibleCount++;
        } else {
          card.style.display = 'none';
        }
      });

      if (emptyFilter) {
        emptyFilter.hidden = visibleCount > 0;
      }
    }

    if (searchInput) {
      searchInput.addEventListener('input', filterCourses);
    }

    if (jenisFilter) {
      jenisFilter.addEventListener('change', filterCourses);
    }

    if (modaFilter) {
      modaFilter.addEventListener('change', filterCourses);
    }

    if (resetButton) {
      resetButton.addEventListener('click', function () {
        if (searchInput) searchInput.value = '';
        if (jenisFilter) jenisFilter.value = '';
        if (modaFilter) modaFilter.value = '';
        filterCourses();
      });
    }
  });
</script>

@endsection
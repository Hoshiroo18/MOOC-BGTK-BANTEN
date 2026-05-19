@extends('layouts.app')

@section('title', 'Dashboard - MOOC BGTK Banten')

@section('content')
<style>
    /* Tambah di bagian <style> atau file CSS */
.access-item a.is-disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;        /* klik diblokir */
    background: #e5e7eb;
    color: #6b7280;
    border-color: #d1d5db;
}
</style>

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
      <p>
        Jika kegiatan membutuhkan pendaftaran, klik tombol Isi Pendaftaran
        dan lengkapi data peserta.
      </p>
    </div>

    <div class="flow-card">
      <span>03</span>
      <h3>Akses Pembelajaran</h3>
      <p>
        Ikuti kegiatan melalui Zoom, lokasi luring, atau course Moodle sesuai
        pengaturan admin.
      </p>
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

      <div class="jenis-filter-buttons" id="jenisFilterButtons">
        <button type="button" class="jenis-filter-btn is-active" data-jenis="">
          Semua
        </button>

        <button type="button" class="jenis-filter-btn" data-jenis="konsultasi">
          Konsultasi
        </button>

        <button type="button" class="jenis-filter-btn" data-jenis="webinar">
          Webinar
        </button>

        <button type="button" class="jenis-filter-btn" data-jenis="seminar">
          Pelatihan
        </button>
      </div>

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

              @if($card['jenis_kegiatan'])
                <span>{{ ucfirst($card['jenis_kegiatan']) }}</span>
              @endif
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
          <span id="modalJenisKegiatan" hidden>Jenis Kegiatan</span>
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

          <div class="access-item" id="moodleAccessBox">
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
            <a href="#" class="register-btn" id="modalRegisterLink">
                Isi Pendaftaran
            </a>

            <button type="button" class="cancel-btn" data-close-modal>
                Tutup
            </button>
            </div>

            <div style="margin-top:.75rem;text-align:center;">
            <small style="color:#6b7280;font-size:.8rem;">
                Sudah terdaftar di kegiatan ini?
            </small>
            <br>
         <a href="#" id="modalLoginLink"
            style="margin-top:.4rem;padding:.5rem 1.2rem;border:2px solid #16a34a;border-radius:8px;
                    background:transparent;color:#16a34a;font-size:.85rem;font-weight:600;cursor:pointer;
                    display:inline-block;text-decoration:none;">
            Login Kegiatan
            </a>
        </div>
      </div>
    </div>
  </div>

</section>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const kegiatanData = @json($kegiatanCards->values());
    const isLoggedIn     = @json(!empty($kegiatanUser));
    const kegiatanUser   = @json($kegiatanUser ?? null);
    const pesertaKegiatanIds = @json($pesertaKegiatanIds ?? []);

    const modal              = document.getElementById('courseDetailModal');
    const modalImage         = document.getElementById('modalImage');
    const modalJenis         = document.getElementById('modalJenis');
    const modalModa          = document.getElementById('modalModa');
    const modalJenisKegiatan = document.getElementById('modalJenisKegiatan');
    const modalTitle         = document.getElementById('modalTitle');
    const modalDescription   = document.getElementById('modalDescription');
    const modalFasil         = document.getElementById('modalFasil');
    const modalKuota         = document.getElementById('modalKuota');
    const modalWaktu         = document.getElementById('modalWaktu');
    const modalCourseName    = document.getElementById('modalCourseName');
    const zoomBox            = document.getElementById('zoomAccessBox');
    const moodleBox          = document.getElementById('moodleAccessBox');
    const modalZoomLink      = document.getElementById('modalZoomLink');
    const modalMoodleLink    = document.getElementById('modalMoodleLink');
    const modalRegisterLink  = document.getElementById('modalRegisterLink');

    const cards       = document.querySelectorAll('.js-open-detail');
    const closeButtons = document.querySelectorAll('[data-close-modal]');

    const searchInput  = document.getElementById('courseSearch');
    const jenisButtons = document.querySelectorAll('.jenis-filter-btn');
    const modaFilter   = document.getElementById('modaFilter');
    const resetButton  = document.getElementById('resetCourseFilter');
    const emptyFilter  = document.getElementById('courseEmptyFilter');

    let selectedJenis = '';

    function getItemById(id) {
      return kegiatanData.find(function (item) {
        return String(item.id) === String(id);
      });
    }

    function setLink(element, url, activeText) {
      if (!element) return;

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
    if (!item || !modal) return;

    // ── RESET STATE (cegah bocor dari modal sebelumnya) ───────────────────
    if (moodleBox) { moodleBox.hidden = false; moodleBox.style.display = ''; }
    if (zoomBox)   { zoomBox.hidden = true;    zoomBox.style.display = ''; }
    modalMoodleLink.classList.remove('is-disabled');
    modalZoomLink.classList.remove('is-disabled');
    modalMoodleLink.removeAttribute('title');
    modalZoomLink.removeAttribute('title');
    modalMoodleLink.href = '#';
    modalZoomLink.href   = '#';

    // ── Variabel dasar ────────────────────────────────────────────────────
    const jenisRaw      = String(item.jenis_raw     || '').toLowerCase();
    const modaRaw       = String(item.moda_raw      || '').toLowerCase();
    const jenisKegiatan = String(item.jenis_kegiatan || '').toLowerCase();

    const isKonsultasi = jenisRaw === 'konsultasi';
    const isTerbimbing = jenisKegiatan === 'terbimbing';

    // ── Cek status peserta di kegiatan ini (dari pesertaKegiatanIds object) ─
    // pesertaKegiatanIds = { kegiatan_id: 'status', ... } dari controller
    const statusPeserta  = pesertaKegiatanIds[String(item.id)] ?? null;
    const isLoginedToThis = isLoggedIn && statusPeserta !== null;
    const isDisetujui     = statusPeserta === 'disetujui';
    const isMenunggu      = statusPeserta === 'menunggu';

    // ── Helper apply state link ───────────────────────────────────────────
    function applyLinkState(linkEl, activeUrl, activeText) {
        if (!linkEl) return;
        linkEl.classList.remove('is-disabled');
        linkEl.removeAttribute('title');

        if (!isLoggedIn) {
            // Belum login sama sekali
            linkEl.href        = item.slug ? '/kegiatan/' + item.slug + '/login' : '#';
            linkEl.textContent = 'Login dulu';
            linkEl.classList.add('is-disabled');
            linkEl.title       = 'Login kegiatan terlebih dahulu.';
        } else if (!isLoginedToThis) {
            // Sudah login tapi belum daftar kegiatan ini
            linkEl.href        = item.link_pendaftaran || '#';
            linkEl.textContent = 'Daftar dulu';
            linkEl.classList.add('is-disabled');
            linkEl.title       = 'Daftar kegiatan ini terlebih dahulu.';
        } else if (isMenunggu) {
            // Sudah daftar tapi menunggu persetujuan admin
            linkEl.href        = '#';
            linkEl.textContent = 'Menunggu persetujuan';
            linkEl.classList.add('is-disabled');
            linkEl.title       = 'Akses aktif setelah admin menyetujui pendaftaran Anda.';
        } else if (isDisetujui) {
            // Sudah disetujui
            if (activeUrl && String(activeUrl).trim() !== '') {
                linkEl.href        = activeUrl;
                linkEl.textContent = activeText;
            } else {
                linkEl.href        = '#';
                linkEl.textContent = 'Belum tersedia';
                linkEl.classList.add('is-disabled');
            }
        } else {
            linkEl.href        = '#';
            linkEl.textContent = 'Belum tersedia';
            linkEl.classList.add('is-disabled');
        }
    }

    // ── Isi konten modal ──────────────────────────────────────────────────
    modalImage.src               = item.flayer;
    modalImage.alt               = item.title;
    modalJenis.textContent       = item.jenis;
    modalModa.textContent        = item.moda;
    modalTitle.textContent       = item.title;
    modalDescription.textContent = item.deskripsi || 'Detail kegiatan akan segera diperbarui.';
    modalFasil.textContent       = item.fasil  || '-';
    modalKuota.textContent       = item.kuota  || '-';
    modalWaktu.textContent       = item.waktu  || '-';

    if (modalJenisKegiatan) {
        if (jenisKegiatan !== '') {
            modalJenisKegiatan.hidden      = false;
            modalJenisKegiatan.textContent = jenisKegiatan.charAt(0).toUpperCase() + jenisKegiatan.slice(1);
        } else {
            modalJenisKegiatan.hidden      = true;
            modalJenisKegiatan.textContent = '';
        }
    }

    // ── Zoom ──────────────────────────────────────────────────────────────
// ── Zoom ──────────────────────────────────────────────────────────────
const showZoom = (modaRaw === 'daring' || modaRaw === 'hybrid');
if (zoomBox) {
    zoomBox.hidden        = !showZoom;
    zoomBox.style.display = '';
}
if (showZoom) {
    applyLinkState(modalZoomLink, item.link_zoom, 'Buka Zoom');
}

    // ── Moodle ────────────────────────────────────────────────────────────
    if (moodleBox) { moodleBox.hidden = false; moodleBox.style.display = ''; }

    applyLinkState(modalMoodleLink, item.moodle_link, 'Buka Moodle');

    // Teks keterangan Course Moodle
    if (!isLoggedIn) {
        modalCourseName.textContent = 'Login kegiatan terlebih dahulu untuk mengakses.';
    } else if (!isLoginedToThis) {
        modalCourseName.textContent = 'Daftar kegiatan ini terlebih dahulu untuk mengakses.';
    } else if (isMenunggu) {
        modalCourseName.textContent = 'Link Moodle aktif setelah admin menyetujui peserta.';
    } else if (isDisetujui) {
        modalCourseName.textContent = item.course_name || 'Course Moodle';
    } else {
        modalCourseName.textContent = 'Course Moodle yang terhubung dengan kegiatan ini.';
    }

    // ── Tombol Pendaftaran ────────────────────────────────────────────────
    modalRegisterLink.hidden = false;
    modalRegisterLink.href   = item.link_pendaftaran || '#';
    modalRegisterLink.classList.toggle('is-disabled', !item.link_pendaftaran);
    modalRegisterLink.textContent = 'Isi Pendaftaran';
    modalRegisterLink.removeAttribute('target');
    modalRegisterLink.removeAttribute('rel');

    // ── Login Link ────────────────────────────────────────────────────────
    const modalLoginLink = document.getElementById('modalLoginLink');
    if (modalLoginLink) {
        if (item.slug) {
            modalLoginLink.href   = '/kegiatan/' + item.slug + '/login';
            modalLoginLink.hidden = false;
            modalLoginLink.classList.remove('is-disabled');
        } else {
            modalLoginLink.href   = '#';
            modalLoginLink.hidden = true;
        }
    }

    modal.hidden = false;
    document.body.classList.add('modal-open');
}

    function closeModal() {
      if (!modal) return;
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
      const keyword   = searchInput ? searchInput.value.toLowerCase().trim() : '';
      const jenisValue = selectedJenis;
      const modaValue  = modaFilter ? modaFilter.value : '';

      let visibleCount = 0;

      cards.forEach(function (card) {
        const title = card.dataset.title || '';
        const jenis = card.dataset.jenis || '';
        const moda  = card.dataset.moda  || '';

        const matchKeyword = title.includes(keyword);
        const matchJenis   = jenisValue === '' || jenis === jenisValue;
        const matchModa    = modaValue  === '' || moda  === modaValue;

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

    jenisButtons.forEach(function (button) {
      button.addEventListener('click', function () {
        selectedJenis = button.dataset.jenis || '';

        jenisButtons.forEach(function (btn) {
          btn.classList.remove('is-active');
        });

        button.classList.add('is-active');
        filterCourses();
      });
    });

    if (modaFilter) {
      modaFilter.addEventListener('change', filterCourses);
    }

    if (resetButton) {
      resetButton.addEventListener('click', function () {
        if (searchInput) searchInput.value = '';

        selectedJenis = '';

        jenisButtons.forEach(function (btn) {
          btn.classList.remove('is-active');
          if ((btn.dataset.jenis || '') === '') {
            btn.classList.add('is-active');
          }
        });

        if (modaFilter) modaFilter.value = '';

        filterCourses();
      });
    }
  });
</script>

@endsection

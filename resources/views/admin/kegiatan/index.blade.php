@extends('layouts.app')

@section('title', 'Kegiatan - MOOC BGTK Banten')

@push('styles')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link rel="stylesheet" href="{{ asset('css/kegiatan.css') }}">
@endpush

@section('content')

@php
  $kegiatanCollection = collect($kegiatan);

  $totalKegiatan  = $kegiatanCollection->count();
  $totalWebinar   = $kegiatanCollection->filter(fn($k) => optional($k->tipeKegiatan)->nama_kegiatan === 'Webinar')->count();
  $totalPelatihan = $kegiatanCollection->filter(fn($k) => optional($k->tipeKegiatan)->nama_kegiatan === 'Seminar')->count();
  $totalKonsultasi= $kegiatanCollection->filter(fn($k) => optional($k->tipeKegiatan)->nama_kegiatan === 'Konsultasi')->count();
@endphp

<section class="admin-kegiatan-page">

  {{-- HERO --}}
  <div class="kegiatan-hero">
    <div class="container kegiatan-hero-inner">

      <div class="kegiatan-hero-copy">
        <span class="eyebrow light">Manajemen Kegiatan</span>

        <h1>Kelola Kegiatan MOOC BGTK Banten</h1>

        <p>
          Tambahkan webinar, pelatihan, dan konsultasi dalam satu dashboard.
          Data kegiatan, flayer, kuota, jadwal, link pendaftaran, Zoom,
          Moodle, dan lokasi dapat dikelola lebih rapi dan terpusat.
        </p>

        <div class="kegiatan-hero-actions">
          <a href="#form-kegiatan" class="hero-main-btn">
            Tambah Kegiatan
          </a>

          <a href="#data-kegiatan" class="hero-outline-btn">
            Lihat Data
          </a>
        </div>

        <div class="kegiatan-hero-stats">
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
            <span>Seminar</span>
          </div>

          <div>
            <strong>{{ $totalKonsultasi }}</strong>
            <span>Konsultasi</span>
          </div>
        </div>
      </div>

      <div class="kegiatan-hero-panel">
        <div class="hero-panel-card main-preview-card">
          <div class="preview-card-top">
            <div>
              <span>Dashboard Admin</span>
              <h3>Publikasi Kegiatan</h3>
            </div>

            <div class="preview-icon">
              ✦
            </div>
          </div>

          <div class="preview-progress">
            <div>
              <span>Data Kegiatan</span>
              <strong>{{ $totalKegiatan }} tersimpan</strong>
            </div>

            <div class="progress-bar">
              <span></span>
            </div>
          </div>

          <div class="preview-mini-list">
            <div>
              <span class="mini-dot webinar"></span>
              <p>Webinar daring</p>
            </div>

            <div>
              <span class="mini-dot pelatihan"></span>
              <p>Pelatihan kompetensi</p>
            </div>

            <div>
              <span class="mini-dot konsultasi"></span>
              <p>Konsultasi pembinaan</p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  {{-- MAIN --}}
  <div class="container kegiatan-main-container">

    @if(session('success'))
      <div class="alert-success">
        {{ session('success') }}
      </div>
    @endif

    @if($errors->any())
      <div class="alert-error">
        {{ $errors->first() }}
      </div>
    @endif

    <div class="kegiatan-grid">

      {{-- FORM KEGIATAN --}}
      <div class="admin-card kegiatan-form-card" id="form-kegiatan">
        <div class="section-heading compact">
          <div>
            <span class="card-kicker">Input Data</span>
            <h2>Form Kegiatan</h2>
            <p>Isi data kegiatan yang akan dipublikasikan ke halaman user.</p>
          </div>
        </div>

        <form
          action="{{ route('admin.kegiatan.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="admin-form js-store-form"
        >
          @csrf

          <div class="form-row">
            {{-- Tipe Kegiatan dari tabel tipe_kegiatan --}}
            <div class="form-group">
              <label for="tipe_kegiatan_id">
                Tipe Kegiatan <span class="required-mark">*</span>
              </label>

              <select id="tipe_kegiatan_id" name="tipe_kegiatan_id" required>
                <option value="">Pilih Tipe kegiatan</option>
                @foreach($tipeKegiatan as $tipe)
                  <option value="{{ $tipe->tipe_kegiatan_id }}"
                    {{ old('tipe_kegiatan_id') == $tipe->tipe_kegiatan_id ? 'selected' : '' }}>
                    {{ $tipe->nama_kegiatan }}
                  </option>
                @endforeach
              </select>
            </div>

            {{-- Moda dari tabel moda --}}
            <div class="form-group">
              <label for="moda_id">
                Moda <span class="required-mark">*</span>
              </label>

              <select id="moda_id" name="moda_id" required>
                <option value="">Pilih moda</option>
                @foreach($modaList as $moda)
                  <option value="{{ $moda->moda_id }}"
                    {{ old('moda_id') == $moda->moda_id ? 'selected' : '' }}>
                    {{ $moda->jenis_moda }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-row">
            {{-- Jenis Kegiatan dari tabel jenis_kegiatan --}}
            <div class="form-group">
              <label for="jenis_kegiatan_id">
                Jenis Kegiatan
              </label>

              <select id="jenis_kegiatan_id" name="jenis_kegiatan_id">
                <option value="">Pilih jenis (opsional)</option>
                @foreach($jenisKegiatan as $jenis)
                  <option value="{{ $jenis->jenis_kegiatan_id }}"
                    {{ old('jenis_kegiatan_id') == $jenis->jenis_kegiatan_id ? 'selected' : '' }}>
                    {{ $jenis->jenis_kegiatan }}
                  </option>
                @endforeach
              </select>

              <small class="form-help">
                Bisa dipilih untuk webinar, pelatihan, maupun konsultasi.
              </small>
            </div>

             {{-- Perlu Pendaftaran --}}
<div class="form-group">
  <label for="is_registration_required">
    Perlu Pendaftaran?
  </label>

  <select id="is_registration_required" name="is_registration_required">
    <option value="1" {{ old('is_registration_required', '1') == '1' ? 'selected' : '' }}>
      Ya, perlu pendaftaran
    </option>
    <option value="0" {{ old('is_registration_required') == '0' ? 'selected' : '' }}>
      Tidak perlu pendaftaran
    </option>
  </select>

  <small class="form-help">
    Otomatis terisi sesuai jenis kegiatan yang dipilih.
  </small>
</div>

            <div class="form-group">
              <label for="kuota">
                Kuota <span class="required-mark">*</span>
              </label>

              <input
                type="number"
                id="kuota"
                name="kuota"
                value="{{ old('kuota') }}"
                placeholder="Contoh: 100"
                min="1"
                required
              >
            </div>
          </div>

          <div class="form-group">
            <label for="nama_kegiatan">
              Nama Kegiatan <span class="required-mark">*</span>
            </label>

            <input
              type="text"
              id="nama_kegiatan"
              name="nama_kegiatan"
              value="{{ old('nama_kegiatan') }}"
              placeholder="Contoh: Webinar Implementasi Kurikulum Merdeka"
              required
            >
          </div>

          {{-- Fasilitator multiple checkbox dari tabel fasilitator --}}
         <div class="form-group">

  @include('partials.kegiatan.addfasil', ['fasilitators' => $fasilitators])
</div>

      <div class="form-row">
  {{-- <div class="form-group">
    <label for="waktu_pelaksanaan">
      Waktu Pelaksanaan <span class="required-mark">*</span>
    </label>
    <input
      type="text"
      id="waktu_pelaksanaan"
      name="waktu_pelaksanaan"
      class="datetime-picker"
      value="{{ old('waktu_pelaksanaan') }}"
      placeholder="Pilih tanggal dan jam kegiatan"
      required
    >
  </div> --}}

  <div class="form-group">
    <label for="start_date">
      Tanggal Mulai
    </label>
    <input
      type="date"
      id="start_date"
      name="start_date"
      value="{{ old('start_date') }}"
    >
    <small class="form-help">Tanggal kegiatan dimulai.</small>
  </div>

  <div class="form-group">
    <label for="end_date">
      Tanggal Selesai
    </label>
    <input
      type="date"
      id="end_date"
      name="end_date"
      value="{{ old('end_date') }}"
    >
    <small class="form-help">Tanggal kegiatan berakhir.</small>
  </div>
</div>

          <div class="form-group">
            <label for="deskripsi">
              Deskripsi <span class="required-mark">*</span>
            </label>

            <textarea
              id="deskripsi"
              name="deskripsi"
              rows="4"
              placeholder="Tulis deskripsi kegiatan..."
              required
            >{{ old('deskripsi') }}</textarea>
          </div>

          <div class="form-group">
            <label for="link_zoom">
              Link Zoom
            </label>

            <input
              type="text"
              id="link_zoom"
              name="link_zoom"
              value="{{ old('link_zoom') }}"
              placeholder="https://zoom.us/..."
            >
          </div>

          <div class="form-group">
            <label for="link_lms">
              Link Course Moodle / LMS
            </label>

            <input
              type="text"
              id="link_lms"
              name="link_lms"
              value="{{ old('link_lms') }}"
              placeholder="https://moodle.example.com/course/view.php?id=..."
            >

            <small class="form-help">
              Untuk pelatihan mandiri atau pelatihan terbimbing setelah admin inject peserta.
            </small>
          </div>

          <div class="form-group">
            <label for="flayer">
              Flayer <span class="required-mark">*</span>
            </label>

            <input
              type="file"
              id="flayer"
              name="flayer"
              accept="image/*"
              required
            >

            <small class="form-help">
              Flayer yang akan di upload wajib memiliki ukuran <b>1080x1350</b>.
            </small>
          </div>

          {{-- Tambahan field untuk Token dan Status URL --}}
<div class="form-row">
    <div class="form-group">
        <label for="token_kegiatan">
            Token Kegiatan
        </label>

        <input
            type="text"
            id="token_kegiatan"
            name="token_kegiatan"
            value="{{ old('token_kegiatan') }}"
            placeholder="Token opsional (max 10 karakter)"
            maxlength="10"
        >

        <small class="form-help">
            Token untuk keperluan khusus kegiatan. Bisa diisi manual sesuai kebutuhan.
        </small>
    </div>

    <div class="form-group">
        <label for="status_url">
            Status URL Pendaftaran
        </label>

        <select id="status_url" name="status_url">
            <option value="active" {{ old('status_url', 'active') == 'active' ? 'selected' : '' }}>
                Aktif (URL dapat diakses)
            </option>
            <option value="inactive" {{ old('status_url') == 'inactive' ? 'selected' : '' }}>
                Nonaktif (URL tidak dapat diakses)
            </option>
        </select>

        <small class="form-help">
            Jika Nonaktif, link pendaftaran tidak akan bisa diakses oleh user.
        </small>
    </div>
</div>

          <div class="form-group">
            <label>Link Pendaftaran</label>

            <input
              type="text"
              value="Auto generate setelah kegiatan disimpan"
              readonly
            >
          </div>

          <button type="button" class="admin-submit-btn" id="openSaveModalButton">
            Simpan Kegiatan
          </button>
        </form>
      </div>

      {{-- DATA KEGIATAN --}}
      <div class="admin-card kegiatan-data-card" id="data-kegiatan">
        <div class="section-heading compact">
          <div>
            <span class="card-kicker">Database</span>
            <h2>Data Kegiatan</h2>
            <p>Data kegiatan yang sudah tersimpan dan siap dipublikasikan.</p>
          </div>
        </div>

        <div class="kegiatan-search-box">
          <input
            type="text"
            id="searchKegiatanInput"
            placeholder="Cari nama kegiatan..."
            autocomplete="off"
          >

          <button type="button" id="resetSearchKegiatan">
            Reset
          </button>
        </div>

        <div class="kegiatan-search-empty" id="searchKegiatanEmpty" hidden>
          Kegiatan tidak ditemukan.
        </div>

        <div class="kegiatan-list" id="kegiatanList">
          @forelse($kegiatan as $item)
            <article
              class="kegiatan-item"
              data-nama-kegiatan="{{ strtolower($item->nama_kegiatan) }}"
            >
              @if($item->flayer)
                <div class="kegiatan-flayer-wrap">
                  <img
                    src="{{ asset('storage/' . $item->flayer) }}"
                    alt="{{ $item->nama_kegiatan }}"
                    class="kegiatan-flayer"
                  >
                </div>
              @else
                <div class="kegiatan-flayer-wrap kegiatan-flayer-empty">
                  <span>MOOC</span>
                </div>
              @endif

              <div class="kegiatan-item-body">
                <div class="kegiatan-badges">
                  <span>{{ optional($item->tipeKegiatan)->nama_kegiatan ?? '-' }}</span>
                  <span>{{ optional($item->moda)->jenis_moda ?? '-' }}</span>

                  @if($item->jenisKegiatan)
                    <span>{{ $item->jenisKegiatan->jenis_kegiatan }}</span>
                  @endif
                </div>

                <h3>{{ $item->nama_kegiatan }}</h3>

                <p>
                  {{ \Illuminate\Support\Str::limit($item->deskripsi, 110) }}
                </p>

                <div class="kegiatan-meta">
                  <small>
                    Fasil:
                    {{ $item->fasilitators->pluck('nama')->join(', ') ?: '-' }}
                  </small>
                  <small>Kuota: {{ $item->kuota }}</small>
                <small>
                    Waktu:
                    {{ $item->start_date ? \Carbon\Carbon::parse($item->start_date)->format('d M Y') : '-' }}
                    s/d
                    {{ $item->end_date ? \Carbon\Carbon::parse($item->end_date)->format('d M Y') : '-' }}
                    </small>

                  @if($item->link_lms)
                    <small>Moodle: tersedia</small>
                  @endif
                </div>

                <div class="kegiatan-link-box">
                  <input type="text" value="{{ $item->link_pendaftaran }}" readonly>

                  <a href="{{ $item->link_pendaftaran }}" target="_blank" rel="noopener">
                    Buka
                  </a>
                </div>


                  <a href="{{ route('admin.kegiatan.edit', $item->kegiatan_id) }}"
                  class="edit-kegiatan-btn"
                >
                  Edit Kegiatan
                </a>

                <form
                  action="{{ route('admin.kegiatan.destroy', $item->kegiatan_id) }}"
                  method="POST"
                  class="delete-kegiatan-form js-delete-form"
                  data-kegiatan="{{ $item->nama_kegiatan }}"
                >
                  @csrf
                  @method('DELETE')

                  <button type="submit" class="delete-kegiatan-btn">
                    Hapus Kegiatan
                  </button>
                </form>
                {{-- Tombol Inject Moodle - di luar form hapus --}}
@if($item->link_lms)
  @php
    $totalMenunggu  = \App\Models\PesertaKegiatan::where('kegiatan_id', $item->kegiatan_id)
      ->where('status', 'menunggu')->count();
    $totalDisetujui = \App\Models\PesertaKegiatan::where('kegiatan_id', $item->kegiatan_id)
      ->where('status', 'disetujui')->count();
  @endphp

  <div style="margin-top:.5rem;font-size:.8rem;color:#6b7280;">
    <span>Disetujui: <strong>{{ $totalDisetujui }}</strong> | Menunggu: <strong>{{ $totalMenunggu }}</strong></span>
  </div>

  <form
    action="{{ route('admin.kegiatan.moodle.injected', $item->kegiatan_id) }}"
    method="POST"
    style="margin-top:.5rem;"
  >
    @csrf
    <button
      type="submit"
      class="inject-kegiatan-btn"
      {{ $totalMenunggu === 0 ? 'disabled' : '' }}
    >
      {{ $totalMenunggu === 0 ? 'Semua Sudah Disetujui' : 'Setujui Peserta Menunggu (' . $totalMenunggu . ')' }}
    </button>
  </form>
@else
  <small style="color:#9ca3af;font-size:.78rem;display:block;margin-top:.5rem;">
    Isi Link Moodle dulu untuk mengaktifkan persetujuan peserta.
  </small>
@endif

              </div>
            </article>
          @empty
            <div class="empty-state">
              Belum ada kegiatan. Tambahkan kegiatan dari form sebelah kiri.
            </div>
          @endforelse
        </div>
      </div>

    </div>
  </div>

  {{-- POPUP WAJIB ISI --}}
  <div class="required-popup" id="requiredPopup" hidden>
    <div class="required-popup-card">
      <strong>Data belum lengkap</strong>
      <p id="requiredPopupText">Mohon lengkapi field yang wajib diisi.</p>
    </div>
  </div>

  {{-- MODAL KONFIRMASI SIMPAN --}}
  <div class="save-modal" id="saveConfirmModal" hidden>
    <div class="modal-backdrop" data-close-save-modal></div>

    <div
      class="modal-card"
      role="dialog"
      aria-modal="true"
      aria-labelledby="saveModalTitle"
    >
      <div class="modal-icon save-icon">
        ✓
      </div>

      <h2 id="saveModalTitle">Simpan kegiatan?</h2>

      <p>
        Pastikan data kegiatan sudah benar sebelum disimpan ke database dan dipublikasikan.
      </p>

      <div class="modal-target">
        Data kegiatan akan tersimpan setelah Anda menekan tombol konfirmasi.
      </div>

      <div class="modal-actions">
        <button type="button" class="modal-cancel-btn" data-close-save-modal>
          Batal
        </button>

        <button type="button" class="modal-confirm-btn" id="confirmSaveButton">
          Ya, Simpan
        </button>
      </div>
    </div>
  </div>

  {{-- MODAL KONFIRMASI HAPUS --}}
  <div class="delete-modal" id="deleteConfirmModal" hidden>
    <div class="modal-backdrop" data-close-delete-modal></div>

    <div
      class="modal-card"
      role="dialog"
      aria-modal="true"
      aria-labelledby="deleteModalTitle"
    >
      <div class="modal-icon delete-icon">
        !
      </div>

      <h2 id="deleteModalTitle">Yakin ingin hapus?</h2>

      <p>
        Data kegiatan ini akan dihapus dari database. Aksi ini tidak bisa dibatalkan.
      </p>

      <div class="modal-target" id="deleteModalTarget">
        Kegiatan
      </div>

      <div class="modal-actions">
        <button type="button" class="modal-cancel-btn" data-close-delete-modal>
          Tidak
        </button>

        <button type="button" class="modal-confirm-btn" id="confirmDeleteButton">
          Ya, Hapus
        </button>
      </div>
    </div>
  </div>

</section>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const waktuInput = document.getElementById('waktu_pelaksanaan');

    if (waktuInput) {
      flatpickr(waktuInput, {
        enableTime: true,
        time_24hr: false,
        dateFormat: 'Y-m-d H:i',
        altInput: true,
        altFormat: 'd F Y, h:i K',
        locale: 'id',
        minuteIncrement: 5,
        defaultHour: 9,
        defaultMinute: 0,
        disableMobile: true
      });
    }
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchKegiatanInput');
    const resetButton = document.getElementById('resetSearchKegiatan');
    const emptySearch = document.getElementById('searchKegiatanEmpty');
    const kegiatanItems = document.querySelectorAll('.kegiatan-item');

    function filterKegiatan() {
      if (!searchInput) return;

      const keyword = searchInput.value.toLowerCase().trim();
      let visibleCount = 0;

      kegiatanItems.forEach(function (item) {
        const namaKegiatan = item.getAttribute('data-nama-kegiatan') || '';

        if (namaKegiatan.includes(keyword)) {
          item.style.display = '';
          visibleCount++;
        } else {
          item.style.display = 'none';
        }
      });

      if (emptySearch) {
        emptySearch.hidden = keyword === '' || visibleCount > 0;
      }
    }

    if (searchInput) searchInput.addEventListener('input', filterKegiatan);

    if (resetButton && searchInput) {
      resetButton.addEventListener('click', function () {
        searchInput.value = '';
        filterKegiatan();
        searchInput.focus();
      });
    }
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const saveForm = document.querySelector('.js-store-form');
    const openSaveButton = document.getElementById('openSaveModalButton');
    const saveModal = document.getElementById('saveConfirmModal');
    const confirmSaveButton = document.getElementById('confirmSaveButton');
    const closeSaveButtons = document.querySelectorAll('[data-close-save-modal]');
    const requiredPopup = document.getElementById('requiredPopup');
    const requiredPopupText = document.getElementById('requiredPopupText');

    if (!saveForm || !openSaveButton || !saveModal || !confirmSaveButton) return;

    function showRequiredPopup(message) {
      if (!requiredPopup || !requiredPopupText) { alert(message); return; }
      requiredPopupText.textContent = message;
      requiredPopup.hidden = false;
      clearTimeout(window.requiredPopupTimer);
      window.requiredPopupTimer = setTimeout(function () { requiredPopup.hidden = true; }, 3000);
    }

    function clearFieldErrors() {
      saveForm.querySelectorAll('.form-group.has-error').forEach(function (g) { g.classList.remove('has-error'); });
    }

    function setFieldError(field) {
      const group = field.closest('.form-group');
      if (group) group.classList.add('has-error');
    }

    function fieldEmpty(field) {
      if (field.disabled || field.type === 'file') return false;
      return String(field.value || '').trim() === '';
    }

    function fieldLabel(field) {
      const group = field.closest('.form-group');
      const label = group ? group.querySelector('label') : null;
      if (!label) return 'Field ini';
      return label.textContent.replace('*', '').trim();
    }

    function validateRequiredFields() {
      clearFieldErrors();
      const requiredFields = saveForm.querySelectorAll('[required]');
      let firstInvalidField = null;

      requiredFields.forEach(function (field) {
        if (fieldEmpty(field)) {
          setFieldError(field);
          if (!firstInvalidField) firstInvalidField = field;
        }
      });

      if (firstInvalidField) {
        showRequiredPopup(fieldLabel(firstInvalidField) + ' wajib diisi.');
        const group = firstInvalidField.closest('.form-group');
        if (group) group.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(function () { firstInvalidField.focus(); }, 350);
        return false;
      }

      return true;
    }

    function openSaveModal() {
      saveModal.hidden = false;
      document.body.classList.add('modal-open');
      confirmSaveButton.disabled = false;
      confirmSaveButton.textContent = 'Ya, Simpan';
    }

    function closeSaveModal() {
      saveModal.hidden = true;
      document.body.classList.remove('modal-open');
      confirmSaveButton.disabled = false;
      confirmSaveButton.textContent = 'Ya, Simpan';
    }

    openSaveButton.addEventListener('click', function () {
      if (validateRequiredFields()) openSaveModal();
    });

    saveForm.querySelectorAll('input, select, textarea').forEach(function (field) {
      field.addEventListener('input', function () {
        if (!fieldEmpty(field)) {
          const group = field.closest('.form-group');
          if (group) group.classList.remove('has-error');
        }
      });
      field.addEventListener('change', function () {
        if (!fieldEmpty(field)) {
          const group = field.closest('.form-group');
          if (group) group.classList.remove('has-error');
        }
      });
    });

    closeSaveButtons.forEach(function (btn) { btn.addEventListener('click', closeSaveModal); });

    confirmSaveButton.addEventListener('click', function () {
      confirmSaveButton.disabled = true;
      confirmSaveButton.textContent = 'Menyimpan...';
      saveForm.submit();
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !saveModal.hidden) closeSaveModal();
    });
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('deleteConfirmModal');
    const targetText = document.getElementById('deleteModalTarget');
    const confirmButton = document.getElementById('confirmDeleteButton');
    const deleteForms = document.querySelectorAll('.js-delete-form');
    const closeButtons = document.querySelectorAll('[data-close-delete-modal]');

    let selectedForm = null;

    if (!modal || !targetText || !confirmButton) return;

    function openModal(form) {
      selectedForm = form;
      targetText.textContent = form.getAttribute('data-kegiatan') || 'Kegiatan ini';
      modal.hidden = false;
      document.body.classList.add('modal-open');
      confirmButton.disabled = false;
      confirmButton.textContent = 'Ya, Hapus';
    }

    function closeModal() {
      modal.hidden = true;
      document.body.classList.remove('modal-open');
      selectedForm = null;
      confirmButton.disabled = false;
      confirmButton.textContent = 'Ya, Hapus';
    }

    deleteForms.forEach(function (form) {
      form.addEventListener('submit', function (e) { e.preventDefault(); openModal(form); });
    });

    closeButtons.forEach(function (btn) { btn.addEventListener('click', closeModal); });

    confirmButton.addEventListener('click', function () {
      if (selectedForm) {
        confirmButton.disabled = true;
        confirmButton.textContent = 'Menghapus...';
        selectedForm.submit();
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !modal.hidden) closeModal();
    });
  });
</script>



<script>
  document.addEventListener('DOMContentLoaded', function () {
    const jenisKegiatan   = document.getElementById('jenis_kegiatan_id');
    const isRegistration  = document.getElementById('is_registration_required');

    if (!jenisKegiatan || !isRegistration) return;

    function syncRegistration() {
      const val = jenisKegiatan.value;

      // jenis_kegiatan_id = 1 → Terbimbing → perlu daftar
      if (val === '1') {
        isRegistration.value = '1';
      }

      // jenis_kegiatan_id = 2 → Mandiri → tidak perlu daftar
      if (val === '2') {
        isRegistration.value = '0';
      }
    }

    jenisKegiatan.addEventListener('change', syncRegistration);

    // jalankan sekali saat load (untuk old value)
    syncRegistration();
  });
</script>

@endsection

@extends('layouts.app')

@section('title', 'Edit Kegiatan - MOOC BGTK Banten')

@push('styles')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link rel="stylesheet" href="{{ asset('css/kegiatan.css') }}">
@endpush

@section('content')

<section class="admin-kegiatan-page">

  <div class="kegiatan-hero">
    <div class="container kegiatan-hero-inner">

      <div class="kegiatan-hero-copy">
        <span class="eyebrow light">Edit Kegiatan</span>

        <h1>Perbarui Data Kegiatan</h1>

        <p>
          Ubah data kegiatan, jadwal, moda, kebutuhan pendaftaran, link Zoom,
          link Moodle, lokasi, dan flayer kegiatan.
        </p>

        <div class="kegiatan-hero-actions">
          <a href="{{ route('admin.kegiatan.index') }}" class="hero-main-btn">
            Kembali ke Data Kegiatan
          </a>

          <a href="{{ $kegiatan->link_pendaftaran }}" target="_blank" rel="noopener" class="hero-outline-btn">
            Buka Link Pendaftaran
          </a>
        </div>
      </div>

      <div class="kegiatan-hero-panel">
        <div class="hero-panel-card main-preview-card">
          <div class="preview-card-top">
            <div>
              <span>Data Dipilih</span>
              <h3>{{ $kegiatan->nama_kegiatan }}</h3>
            </div>

            <div class="preview-icon">
              ✎
            </div>
          </div>

          <div class="preview-mini-list">
            <div>
              <span class="mini-dot webinar"></span>
              <p>{{ ucfirst($kegiatan->jenis_kegiatan) }}</p>
            </div>

            <div>
              <span class="mini-dot pelatihan"></span>
              <p>{{ ucfirst($kegiatan->moda) }}</p>
            </div>

            <div>
              <span class="mini-dot konsultasi"></span>
              <p>{{ $kegiatan->perlu_pendaftaran ? 'Perlu pendaftaran' : 'Tanpa pendaftaran' }}</p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <div class="container kegiatan-main-container">

    @if($errors->any())
      <div class="alert-error">
        {{ $errors->first() }}
      </div>
    @endif

    <div class="admin-card kegiatan-form-card edit-kegiatan-card">
      <div class="section-heading compact">
        <div>
          <span class="card-kicker">Form Edit</span>
          <h2>Edit Kegiatan</h2>
          <p>Pastikan data kegiatan yang diperbarui sudah benar.</p>
        </div>
      </div>

      <form
        action="{{ route('admin.kegiatan.update', $kegiatan->id) }}"
        method="POST"
        enctype="multipart/form-data"
        class="admin-form"
      >
        @csrf
        @method('PUT')

        <div class="form-row">
          <div class="form-group">
            <label for="jenis_kegiatan">
              Jenis Kegiatan <span class="required-mark">*</span>
            </label>

            <select id="jenis_kegiatan" name="jenis_kegiatan" required>
              <option value="">
                Pilih jenis kegiatan
              </option>

              <option value="webinar" {{ old('jenis_kegiatan', $kegiatan->jenis_kegiatan) == 'webinar' ? 'selected' : '' }}>
                Webinar
              </option>

              <option value="pelatihan" {{ old('jenis_kegiatan', $kegiatan->jenis_kegiatan) == 'pelatihan' ? 'selected' : '' }}>
                Pelatihan
              </option>

              <option value="konsultasi" {{ old('jenis_kegiatan', $kegiatan->jenis_kegiatan) == 'konsultasi' ? 'selected' : '' }}>
                Konsultasi
              </option>
            </select>
          </div>

          <div class="form-group">
            <label for="moda">
              Moda <span class="required-mark">*</span>
            </label>

            <select id="moda" name="moda" required>
              <option value="">
                Pilih moda
              </option>

              <option value="luring" {{ old('moda', $kegiatan->moda) == 'luring' ? 'selected' : '' }}>
                Luring
              </option>

              <option value="daring" {{ old('moda', $kegiatan->moda) == 'daring' ? 'selected' : '' }}>
                Daring
              </option>

              <option value="hybrid" {{ old('moda', $kegiatan->moda) == 'hybrid' ? 'selected' : '' }}>
                Hybrid
              </option>
            </select>
          </div>
        </div>

<div class="form-row">
  <div class="form-group" id="jenisPelatihanGroup">
    <label for="jenis_pelatihan">
      Jenis Pelatihan
    </label>

    <select id="jenis_pelatihan" name="jenis_pelatihan">
      <option value="">
        Pilih jenis pelatihan
      </option>

      <option value="terbimbing" {{ old('jenis_pelatihan', $kegiatan->jenis_pelatihan) == 'terbimbing' ? 'selected' : '' }}>
        Terbimbing
      </option>

      <option value="mandiri" {{ old('jenis_pelatihan', $kegiatan->jenis_pelatihan) == 'mandiri' ? 'selected' : '' }}>
        Mandiri
      </option>
    </select>

    <small class="form-help">
      Bisa dipilih untuk webinar, pelatihan, maupun konsultasi.
    </small>
  </div>

  <div class="form-group">
    <label for="perlu_pendaftaran">
      Perlu Isi Pendaftaran?
    </label>

    <select id="perlu_pendaftaran" name="perlu_pendaftaran">
      <option value="1" {{ old('perlu_pendaftaran', $kegiatan->perlu_pendaftaran ? '1' : '0') == '1' ? 'selected' : '' }}>
        Ya, perlu pendaftaran
      </option>

      <option value="0" {{ old('perlu_pendaftaran', $kegiatan->perlu_pendaftaran ? '1' : '0') == '0' ? 'selected' : '' }}>
        Tidak perlu pendaftaran
      </option>
    </select>

    <small class="form-help">
      Terbimbing otomatis perlu daftar. Mandiri otomatis tidak perlu.
    </small>
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
            value="{{ old('nama_kegiatan', $kegiatan->nama_kegiatan) }}"
            placeholder="Contoh: Webinar Implementasi Kurikulum Merdeka"
            required
          >
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="fasil">
              Fasilitator <span class="required-mark">*</span>
            </label>

            <input
              type="text"
              id="fasil"
              name="fasil"
              value="{{ old('fasil', $kegiatan->fasil) }}"
              placeholder="Contoh: Dr. Yusup Ardabili"
              required
            >
          </div>

          <div class="form-group">
            <label for="kuota">
              Kuota <span class="required-mark">*</span>
            </label>

            <input
              type="number"
              id="kuota"
              name="kuota"
              value="{{ old('kuota', $kegiatan->kuota) }}"
              placeholder="Contoh: 100"
              min="1"
              required
            >
          </div>
        </div>

        <div class="form-group">
          <label for="waktu_pelaksanaan">
            Waktu Pelaksanaan <span class="required-mark">*</span>
          </label>

          <input
            type="text"
            id="waktu_pelaksanaan"
            name="waktu_pelaksanaan"
            class="datetime-picker"
            value="{{ old('waktu_pelaksanaan', \Carbon\Carbon::parse($kegiatan->waktu_pelaksanaan)->format('Y-m-d H:i')) }}"
            placeholder="Pilih tanggal dan jam kegiatan"
            required
          >
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
  >{{ old('deskripsi', $kegiatan->deskripsi) }}</textarea>
</div>

        <div class="form-group">
          <label for="link_zoom">
            Link Zoom
          </label>

          <input
            type="text"
            id="link_zoom"
            name="link_zoom"
            value="{{ old('link_zoom', $kegiatan->link_zoom) }}"
            placeholder="https://zoom.us/..."
          >
        </div>

        <div class="form-group">
          <label for="moodle_course_url">
            Link Course Moodle
          </label>

          <input
            type="text"
            id="moodle_course_url"
            name="moodle_course_url"
            value="{{ old('moodle_course_url', $kegiatan->moodle_course_url) }}"
            placeholder="https://moodle.example.com/course/view.php?id=..."
          >

          <small class="form-help">
            Untuk pelatihan mandiri atau pelatihan terbimbing setelah admin inject peserta.
          </small>
        </div>

<div class="form-group">
  <label for="flayer">
    Ganti Flayer {{ $kegiatan->flayer ? '' : '*' }}
  </label>

  <input
    type="file"
    id="flayer"
    name="flayer"
    accept="image/*"
    {{ $kegiatan->flayer ? '' : 'required' }}
  >

  <small class="form-help">
    Kosongkan jika tidak ingin mengganti flayer. Kalau belum ada flayer, field ini wajib diisi. Ukuran wajib 1080x1350.
  </small>
</div>

        @if($kegiatan->flayer)
          <div class="edit-flayer-preview">
            <span>Flayer saat ini</span>

            <img
              src="{{ asset('storage/' . $kegiatan->flayer) }}"
              alt="{{ $kegiatan->nama_kegiatan }}"
            >
          </div>
        @endif

        <div class="form-group">
          <label>Link Pendaftaran</label>

          <input
            type="text"
            value="{{ $kegiatan->link_pendaftaran }}"
            readonly
          >
        </div>

        <div class="edit-actions-row">
          <a href="{{ route('admin.kegiatan.index') }}" class="edit-cancel-btn">
            Batal
          </a>

          <button type="submit" class="admin-submit-btn">
            Simpan Perubahan
          </button>
        </div>
      </form>
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

    const jenisPelatihan = document.getElementById('jenis_pelatihan');
    const perluPendaftaran = document.getElementById('perlu_pendaftaran');

    if (!jenisPelatihan || !perluPendaftaran) {
      return;
    }

    function syncJenisPelatihan() {
      const pelatihan = jenisPelatihan.value;

      /*
        Jenis Pelatihan bebas dipilih untuk semua jenis kegiatan.
        Tidak mengubah Jenis Kegiatan.
      */

      if (pelatihan === 'terbimbing') {
        perluPendaftaran.value = '1';
      }

      if (pelatihan === 'mandiri') {
        perluPendaftaran.value = '0';
      }
    }

    jenisPelatihan.addEventListener('change', syncJenisPelatihan);

    syncJenisPelatihan();
  });
</script>

@endsection
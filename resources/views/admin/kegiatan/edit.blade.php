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
              <p>{{ optional($kegiatan->tipeKegiatan)->nama_kegiatan ?? '-' }}</p>
            </div>

            <div>
              <span class="mini-dot pelatihan"></span>
              <p>{{ optional($kegiatan->moda)->jenis_moda ?? '-' }}</p>
            </div>

            <div>
              <span class="mini-dot konsultasi"></span>
              <p>{{ $kegiatan->is_registration_required ? 'Perlu pendaftaran' : 'Tanpa pendaftaran' }}</p>
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
        action="{{ route('admin.kegiatan.update', $kegiatan->kegiatan_id) }}"
        method="POST"
        enctype="multipart/form-data"
        class="admin-form"
      >
        @csrf
        @method('PUT')

        <div class="form-row">
          {{-- Tipe Kegiatan --}}
          <div class="form-group">
            <label for="tipe_kegiatan_id">
              Tipe Kegiatan <span class="required-mark">*</span>
            </label>

            <select id="tipe_kegiatan_id" name="tipe_kegiatan_id" required>
              <option value="">Pilih Tipe Kegiatan</option>
              @foreach($tipeKegiatan as $tipe)
                <option value="{{ $tipe->tipe_kegiatan_id }}"
                  {{ old('tipe_kegiatan_id', $kegiatan->tipe_kegiatan_id) == $tipe->tipe_kegiatan_id ? 'selected' : '' }}>
                  {{ $tipe->nama_kegiatan }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Moda --}}
          <div class="form-group">
            <label for="moda_id">
              Moda <span class="required-mark">*</span>
            </label>

            <select id="moda_id" name="moda_id" required>
              <option value="">Pilih Moda</option>
              @foreach($modaList as $moda)
                <option value="{{ $moda->moda_id }}"
                  {{ old('moda_id', $kegiatan->moda_id) == $moda->moda_id ? 'selected' : '' }}>
                  {{ $moda->jenis_moda }}
                </option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="form-row">
          {{-- Jenis Kegiatan --}}
          <div class="form-group">
            <label for="jenis_kegiatan_id">
              Jenis Kegiatan
            </label>

            <select id="jenis_kegiatan_id" name="jenis_kegiatan_id">
              <option value="">Pilih jenis (opsional)</option>
              @foreach($jenisKegiatan as $jenis)
                <option value="{{ $jenis->jenis_kegiatan_id }}"
                  {{ old('jenis_kegiatan_id', $kegiatan->jenis_kegiatan_id) == $jenis->jenis_kegiatan_id ? 'selected' : '' }}>
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
              <option value="1"
                {{ old('is_registration_required', $kegiatan->is_registration_required ? '1' : '0') == '1' ? 'selected' : '' }}>
                Ya, perlu pendaftaran
              </option>
              <option value="0"
                {{ old('is_registration_required', $kegiatan->is_registration_required ? '1' : '0') == '0' ? 'selected' : '' }}>
                Tidak perlu pendaftaran
              </option>
            </select>

            <small class="form-help">
              Otomatis terisi sesuai jenis kegiatan yang dipilih.
            </small>
          </div>
        </div>

        {{-- Kuota --}}
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

        {{-- Nama Kegiatan --}}
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

        {{-- Fasilitator --}}
        <div class="form-group">
          @php
            $selectedFasilitators = $kegiatan->fasilitators->pluck('fasilitator_id')->toArray();
          @endphp

          @include('partials.kegiatan.addfasil', [
            'fasilitators'         => $fasilitators,
            'selectedFasilitators' => $selectedFasilitators,
          ])
        </div>

       <div class="form-row">
  {{-- Waktu Pelaksanaan --}}
  {{-- <div class="form-group">
    <label for="waktu_pelaksanaan">
      Waktu Pelaksanaan <span class="required-mark">*</span>
    </label>
    <input
      type="text"
      id="waktu_pelaksanaan"
      name="waktu_pelaksanaan"
      class="datetime-picker"
      value="{{ old('waktu_pelaksanaan', $kegiatan->waktu_pelaksanaan?->format('Y-m-d H:i')) }}"
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
      value="{{ old('start_date', $kegiatan->start_date?->format('Y-m-d')) }}"
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
      value="{{ old('end_date', $kegiatan->end_date?->format('Y-m-d')) }}"
    >
    <small class="form-help">Tanggal kegiatan berakhir.</small>
  </div>
</div>

        {{-- Deskripsi --}}
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

        {{-- Link Zoom --}}
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

        {{-- Link LMS --}}
        <div class="form-group">
          <label for="link_lms">
            Link Course Moodle / LMS
          </label>

          <input
            type="text"
            id="link_lms"
            name="link_lms"
            value="{{ old('link_lms', $kegiatan->link_lms) }}"
            placeholder="https://moodle.example.com/course/view.php?id=..."
          >

          <small class="form-help">
            Untuk pelatihan mandiri atau pelatihan terbimbing setelah admin inject peserta.
          </small>
        </div>

        {{-- TOKEN DAN STATUS URL --}}
        <div class="form-row">
          <div class="form-group">
            <label for="token_kegiatan">
              Token Kegiatan
            </label>

            <input
              type="text"
              id="token_kegiatan"
              name="token_kegiatan"
              value="{{ old('token_kegiatan', $kegiatan->token_kegiatan) }}"
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
              <option value="active" {{ old('status_url', $kegiatan->status_url ?? 'active') == 'active' ? 'selected' : '' }}>
                Aktif (URL dapat diakses)
              </option>
              <option value="inactive" {{ old('status_url', $kegiatan->status_url ?? 'active') == 'inactive' ? 'selected' : '' }}>
                Nonaktif (URL tidak dapat diakses)
              </option>
            </select>

            <small class="form-help">
              Jika Nonaktif, link pendaftaran tidak akan bisa diakses oleh user.
            </small>
          </div>
        </div>

        {{-- Flayer --}}
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
            Kosongkan jika tidak ingin mengganti flayer. Kalau belum ada flayer, field ini wajib diisi. Ukuran wajib <b>1080x1350</b>.
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

        {{-- Link Pendaftaran (readonly) --}}
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
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const jenisKegiatan  = document.getElementById('jenis_kegiatan_id');
    const isRegistration = document.getElementById('is_registration_required');

    if (!jenisKegiatan || !isRegistration) return;

    // ✅ Hapus pemanggilan syncRegistration() saat load
    // Hanya sync saat user mengubah jenis kegiatan
    jenisKegiatan.addEventListener('change', function () {
      const val = jenisKegiatan.value;

      if (val === '1') {
        isRegistration.value = '1';
      } else if (val === '2') {
        isRegistration.value = '0';
      }
      // Kalau val lain / kosong → biarkan user pilih manual
    });
  });
</script>

@endsection

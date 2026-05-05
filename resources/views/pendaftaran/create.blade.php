@extends('layouts.app')

@section('title', 'Pendaftaran Kegiatan - MOOC BGTK Banten')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/pendaftaran.css') }}">
@endpush

@section('content')

<section class="pendaftaran-page">
  <div class="pendaftaran-hero">
    <div class="container pendaftaran-hero-inner">
      <span class="student-eyebrow">Form Pendaftaran</span>

      <h1>{{ $kegiatan->nama_kegiatan }}</h1>

      <p>
        Isi data diri dengan benar. Data ini akan digunakan sebagai data peserta kegiatan
        dan akun peserta untuk melihat kelas yang diikuti.
      </p>
    </div>
  </div>

  <div class="container pendaftaran-container">

    @if(!$kegiatan->perlu_pendaftaran)
      <div class="pendaftaran-card">
        <span class="form-kicker">Tidak Perlu Pendaftaran</span>

        <h2>Kegiatan ini bisa langsung diakses.</h2>

        <p>
          Admin mengatur kegiatan ini tanpa proses pendaftaran. Silakan gunakan akses yang tersedia.
        </p>

        <div class="direct-access-grid">
          @if($kegiatan->link_zoom)
            <a href="{{ $kegiatan->link_zoom }}" target="_blank" rel="noopener">
              Buka Zoom
            </a>
          @endif

          @if($kegiatan->moodle_course_url)
            <a href="{{ $kegiatan->moodle_course_url }}" target="_blank" rel="noopener">
              Buka Moodle
            </a>
          @endif

          @if($kegiatan->lokasi)
            <div class="access-info">
              <strong>Lokasi</strong>
              <span>{{ $kegiatan->lokasi }}</span>
            </div>
          @endif
        </div>
      </div>
    @else
      <div class="pendaftaran-layout">

        <div class="pendaftaran-card">
          <span class="form-kicker">Data Peserta</span>

          <h2>Isi Pendaftaran</h2>

          @if($errors->any())
            <div class="alert-error">
              {{ $errors->first() }}
            </div>
          @endif

          <form
            action="{{ route('kegiatan.daftar.store', $kegiatan->slug) }}"
            method="POST"
            class="pendaftaran-form"
          >
            @csrf

            <div class="form-group">
              <label for="nama">Nama <span>*</span></label>
              <input
                type="text"
                id="nama"
                name="nama"
                value="{{ old('nama') }}"
                placeholder="Nama lengkap"
                required
              >
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="nip">NIP</label>
                <input
                  type="text"
                  id="nip"
                  name="nip"
                  value="{{ old('nip') }}"
                  placeholder="NIP jika ada"
                >
              </div>

              <div class="form-group">
                <label for="nik">NIK <span>*</span></label>
                <input
                  type="text"
                  id="nik"
                  name="nik"
                  value="{{ old('nik') }}"
                  placeholder="NIK"
                  required
                >
              </div>
            </div>

            <div class="form-group">
              <label for="asal_instansi">Asal Instansi / Sekolah <span>*</span></label>
              <input
                type="text"
                id="asal_instansi"
                name="asal_instansi"
                value="{{ old('asal_instansi') }}"
                placeholder="Contoh: SDN 1 Serang"
                required
              >
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="email">Email <span>*</span></label>
                <input
                  type="email"
                  id="email"
                  name="email"
                  value="{{ old('email') }}"
                  placeholder="email@contoh.com"
                  required
                >
              </div>

              <div class="form-group">
                <label for="jenis_kelamin">Jenis Kelamin <span>*</span></label>
                <select id="jenis_kelamin" name="jenis_kelamin" required>
                  <option value="">Pilih jenis kelamin</option>
                  <option value="Laki-laki" {{ old('jenis_kelamin') === 'Laki-laki' ? 'selected' : '' }}>
                    Laki-laki
                  </option>
                  <option value="Perempuan" {{ old('jenis_kelamin') === 'Perempuan' ? 'selected' : '' }}>
                    Perempuan
                  </option>
                </select>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="kabupaten_kota">Kabupaten / Kota <span>*</span></label>
                <input
                  type="text"
                  id="kabupaten_kota"
                  name="kabupaten_kota"
                  value="{{ old('kabupaten_kota') }}"
                  placeholder="Contoh: Kota Serang"
                  required
                >
              </div>

              <div class="form-group">
                <label for="tanggal_lahir">Tanggal Lahir <span>*</span></label>
                <input
                  type="date"
                  id="tanggal_lahir"
                  name="tanggal_lahir"
                  value="{{ old('tanggal_lahir') }}"
                  required
                >
              </div>
            </div>

            <button type="submit" class="pendaftaran-submit">
              Submit Pendaftaran
            </button>
          </form>
        </div>

        <aside class="pendaftaran-info-card">
          <span class="form-kicker">Info Kegiatan</span>

          <h3>{{ $kegiatan->nama_kegiatan }}</h3>

          <div class="info-list">
            <div>
              <span>Jenis</span>
              <strong>{{ ucfirst($kegiatan->jenis_kegiatan) }}</strong>
            </div>

            @if($kegiatan->jenis_kegiatan === 'pelatihan')
              <div>
                <span>Jenis Pelatihan</span>
                <strong>{{ ucfirst($kegiatan->jenis_pelatihan) }}</strong>
              </div>
            @endif

            <div>
              <span>Moda</span>
              <strong>{{ ucfirst($kegiatan->moda) }}</strong>
            </div>

            <div>
              <span>Fasilitator</span>
              <strong>{{ $kegiatan->fasil ?? '-' }}</strong>
            </div>

            <div>
              <span>Waktu</span>
              <strong>{{ \Carbon\Carbon::parse($kegiatan->waktu_pelaksanaan)->format('d M Y, H:i') }}</strong>
            </div>
          </div>
        </aside>

      </div>
    @endif

  </div>
</section>

@endsection
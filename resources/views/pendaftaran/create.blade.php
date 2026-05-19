@extends('layouts.app')

@section('title', 'Pendaftaran Kegiatan - MOOC BGTK Banten')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/pendaftaran.css') }}">
@endpush

@section('content')

<style>
    .alert-error {
    background: #fee2e2;
    border-left: 4px solid #dc2626;
    padding: 1rem;
    border-radius: 8px;
    color: #991b1b;
    font-size: 0.9rem;
}
</style>

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

    @if(!$kegiatan->is_registration_required)
      <div class="pendaftaran-card">
        <span class="form-kicker">Tidak Perlu Pendaftaran</span>
        <h2>Kegiatan ini bisa langsung diakses.</h2>
        <p>Admin mengatur kegiatan ini tanpa proses pendaftaran. Silakan gunakan akses yang tersedia.</p>
        <div class="direct-access-grid">
          @if($kegiatan->link_zoom)
            <a href="{{ $kegiatan->link_zoom }}" target="_blank" rel="noopener">Buka Zoom</a>
          @endif
          {{-- @if($kegiatan->link_lms)
            <a href="{{ $kegiatan->link_lms }}" target="_blank" rel="noopener">Buka Moodle</a>
          @endif --}}
        </div>
      </div>

    @else
      <div class="pendaftaran-layout">

        <div class="pendaftaran-card">
          <span class="form-kicker">Data Peserta</span>
          <h2>Isi Pendaftaran</h2>

          @if(session('error'))
            <div class="alert-error" style="background: #fee2e2; border-left-color: #dc2626; margin-bottom: 1.5rem;">
                {{ session('error') }}
            </div>
        @endif

          @if($errors->any())
            <div class="alert-error">{{ $errors->first() }}</div>
          @endif

          <form
            action="{{ route('kegiatan.daftar.store', $kegiatan->slug) }}"
            method="POST"
            class="pendaftaran-form"
            id="formPendaftaran"
          >
            @csrf

            {{-- Nama --}}
            <div class="form-group">
              <label for="nama">Nama Lengkap <span>*</span></label>
              <input type="text" id="nama" name="nama" value="{{ old('nama') }}" placeholder="Nama lengkap" required>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="nip">NIP</label>
                <input type="text" id="nip" name="nip" value="{{ old('nip') }}" placeholder="18 digit NIP">
              </div>
              <div class="form-group">
                <label for="nik">NIK <span>*</span></label>
                <input type="text" id="nik" name="nik" value="{{ old('nik') }}" placeholder="16 digit NIK" maxlength="16" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="email">Email <span>*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="email@contoh.com" required>
              </div>
              <div class="form-group">
                <label for="jenis_kelamin">Jenis Kelamin <span>*</span></label>
                <select id="jenis_kelamin" name="jenis_kelamin" required>
                  <option value="">Pilih jenis kelamin</option>
                  <option value="Laki-laki"  {{ old('jenis_kelamin') === 'Laki-laki'  ? 'selected' : '' }}>Laki-laki</option>
                  <option value="Perempuan"  {{ old('jenis_kelamin') === 'Perempuan'  ? 'selected' : '' }}>Perempuan</option>
                </select>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="kota_id">Kabupaten / Kota</label>
                <select id="kota_id" name="kota_id">
                  <option value="">Pilih kab/kota (opsional)</option>
                  @foreach($kotaList as $kota)
                    <option value="{{ $kota->kota_id }}" {{ old('kota_id') == $kota->kota_id ? 'selected' : '' }}>
                      {{ $kota->nama_kota }}
                    </option>
                  @endforeach
                </select>
                <small class="form-help">Pilih untuk mempersempit pencarian sekolah.</small>
              </div>
              <div class="form-group">
                <label for="tanggal_lahir">Tanggal Lahir <span>*</span></label>
                <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
              </div>
            </div>

            {{-- ── ASAL INSTANSI ─────────────────────────────────────────────── --}}
            <div class="form-group">
              <label>Asal Instansi / Sekolah <span>*</span></label>

              {{-- Radio button --}}
              <div class="instansi-radio-group">
                <label class="radio-option">
                  <input type="radio" name="tipe_instansi" value="sekolah" id="radioSekolah" checked>
                  <span>Pilih dari Daftar Sekolah</span>
                </label>
                <label class="radio-option">
                  <input type="radio" name="tipe_instansi" value="manual" id="radioManual">
                  <span>Input Manual</span>
                </label>
              </div>

              {{-- Panel: pilih sekolah --}}
              <div id="panelPilihSekolah">
                {{-- Kotak sekolah terpilih + tombol cari --}}
                <div class="sekolah-pilih-row">
                  <div class="sekolah-terpilih-display" id="sekolahTerpilihDisplay">
                    <span id="sekolahTerpilihNama" class="sekolah-placeholder">Belum ada sekolah dipilih</span>
                  </div>
                  <button type="button" class="btn-cari-sekolah" id="btnBukaModal">
                    Cari Sekolah
                  </button>
                </div>
                <small class="form-help">Klik "Cari Sekolah" untuk mencari dan memilih sekolah.</small>

                {{-- Hidden fields --}}
                <input type="hidden" id="sekolah_id" name="sekolah_id" value="{{ old('sekolah_id') }}">
                <input type="hidden" id="Instansi"   name="Instansi"   value="{{ old('Instansi') }}">
              </div>

              {{-- Panel: input manual --}}
              <div id="panelInstansiManual" hidden>
                <input
                  type="text"
                  id="inputInstansiManual"
                  placeholder="Contoh: Dinas Pendidikan Kab. Serang"
                  class="form-control"
                >
                <small class="form-help">Isi nama instansi jika tidak ada di daftar sekolah.</small>
              </div>
            </div>
            {{-- ── / ASAL INSTANSI ───────────────────────────────────────────── --}}

            <button type="submit" class="pendaftaran-submit">
              Submit Pendaftaran
            </button>
          </form>
        </div>

        {{-- Sidebar Info Kegiatan --}}
        <aside class="pendaftaran-info-card">
          <span class="form-kicker">Info Kegiatan</span>
          <h3>{{ $kegiatan->nama_kegiatan }}</h3>
          <div class="info-list">
            <div>
              <span>Tipe</span>
              <strong>{{ optional($kegiatan->tipeKegiatan)->nama_kegiatan ?? '-' }}</strong>
            </div>
            @if($kegiatan->jenisKegiatan)
              <div>
                <span>Jenis</span>
                <strong>{{ $kegiatan->jenisKegiatan->jenis_kegiatan }}</strong>
              </div>
            @endif
            <div>
              <span>Moda</span>
              <strong>{{ optional($kegiatan->moda)->jenis_moda ?? '-' }}</strong>
            </div>
            <div>
              <span>Fasilitator</span>
              <strong>{{ $kegiatan->fasilitators->pluck('nama')->join(', ') ?: '-' }}</strong>
            </div>
            <div>
                <span>Waktu</span>
                <strong>
                    {{ $kegiatan->start_date ? \Carbon\Carbon::parse($kegiatan->start_date)->format('d M Y') : '-' }}
                    s/d
                    {{ $kegiatan->end_date ? \Carbon\Carbon::parse($kegiatan->end_date)->format('d M Y') : '-' }}
                </strong>
            </div>
            <div>
              <span>Kuota</span>
              <strong>{{ $kegiatan->kuota }}</strong>
            </div>
          </div>
        </aside>

      </div>
    @endif

  </div>
</section>

{{-- ══ MODAL CARI SEKOLAH ══════════════════════════════════════════════════════ --}}
<div class="modal-sekolah-overlay" id="modalSekolah" hidden>
  <div class="modal-sekolah-card" role="dialog" aria-modal="true" aria-labelledby="modalSekolahTitle">

    <div class="modal-sekolah-header">
      <h3 id="modalSekolahTitle">Cari Sekolah</h3>
      <button type="button" class="modal-sekolah-close" id="btnTutupModal">×</button>
    </div>

    <div class="modal-sekolah-filter">
      <select id="modalKotaFilter">
        <option value="">Semua Kab / Kota</option>
        @foreach($kotaList as $kota)
          <option value="{{ $kota->kota_id }}">{{ $kota->nama_kota }}</option>
        @endforeach
      </select>

      <input
        type="text"
        id="modalInputCari"
        placeholder="Ketik nama sekolah atau NPSN..."
        autocomplete="off"
      >
    </div>

    <small class="modal-search-hint">Minimal 3 karakter untuk mulai mencari.</small>

    <div class="modal-sekolah-hasil" id="modalHasil">
      <div class="modal-sekolah-empty">Ketik untuk mulai mencari sekolah.</div>
    </div>

  </div>
</div>
{{-- ══ / MODAL CARI SEKOLAH ════════════════════════════════════════════════════ --}}

<style>
  /* ── Radio group ── */
  .instansi-radio-group {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1rem;
  }
  .radio-option {
    display: flex;
    align-items: center;
    gap: .5rem;
    cursor: pointer;
    font-size: .95rem;
  }
  .radio-option input[type="radio"] {
    accent-color: var(--primary, #3b5bdb);
    width: 1rem;
    height: 1rem;
  }

  /* ── Pilih sekolah row ── */
  .sekolah-pilih-row {
    display: flex;
    align-items: center;
    gap: .75rem;
    flex-wrap: wrap;
  }
  .sekolah-terpilih-display {
    flex: 1;
    min-width: 0;
    padding: .6rem .9rem;
    border: 1.5px solid #d0d7de;
    border-radius: 8px;
    background: #f6f8fa;
    font-size: .9rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .sekolah-terpilih-display.has-value {
    border-color: var(--primary, #3b5bdb);
    background: #eef2ff;
    color: #1e40af;
    font-weight: 600;
  }
  .sekolah-placeholder {
    color: #9ca3af;
    font-style: italic;
  }
  .btn-cari-sekolah {
    flex-shrink: 0;
    padding: .6rem 1.2rem;
    border: none;
    border-radius: 8px;
    background: var(--primary, #3b5bdb);
    color: #fff;
    font-size: .9rem;
    font-weight: 600;
    cursor: pointer;
    transition: background .2s;
  }
  .btn-cari-sekolah:hover { background: #2f4acf; }

  /* ── Modal overlay ── */
  .modal-sekolah-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    z-index: 9000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
  }
  .modal-sekolah-overlay[hidden] { display: none; }

  .modal-sekolah-card {
    background: #fff;
    border-radius: 14px;
    width: 100%;
    max-width: 640px;
    max-height: 85vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,.25);
  }

  .modal-sekolah-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem 1.5rem 1rem;
    border-bottom: 1px solid #e5e7eb;
  }
  .modal-sekolah-header h3 { margin: 0; font-size: 1.1rem; }
  .modal-sekolah-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    line-height: 1;
    cursor: pointer;
    color: #6b7280;
    padding: 0 .25rem;
  }
  .modal-sekolah-close:hover { color: #111; }

  .modal-sekolah-filter {
    display: flex;
    gap: .75rem;
    padding: 1rem 1.5rem .5rem;
    flex-wrap: wrap;
  }
  .modal-sekolah-filter select,
  .modal-sekolah-filter input {
    flex: 1;
    min-width: 140px;
    padding: .55rem .85rem;
    border: 1.5px solid #d0d7de;
    border-radius: 8px;
    font-size: .9rem;
  }
  .modal-sekolah-filter input:focus,
  .modal-sekolah-filter select:focus {
    outline: none;
    border-color: var(--primary, #3b5bdb);
  }

  .modal-search-hint {
    padding: 0 1.5rem .75rem;
    color: #9ca3af;
    font-size: .8rem;
    display: block;
  }

  .modal-sekolah-hasil {
    flex: 1;
    overflow-y: auto;
    padding: 0 1rem 1rem;
  }

  .modal-sekolah-empty {
    text-align: center;
    color: #9ca3af;
    padding: 2rem 1rem;
    font-size: .9rem;
  }

  .modal-sekolah-item {
    padding: .85rem 1rem;
    border-radius: 8px;
    cursor: pointer;
    transition: background .15s;
    border-bottom: 1px solid #f3f4f6;
  }
  .modal-sekolah-item:last-child { border-bottom: none; }
  .modal-sekolah-item:hover { background: #eef2ff; }
  .modal-sekolah-item strong {
    display: block;
    font-size: .95rem;
    color: #111827;
    margin-bottom: .2rem;
  }
  .modal-sekolah-item small {
    color: #6b7280;
    font-size: .8rem;
  }
</style>

{{-- ══ MODAL AUTOFILL NIP/NIK (GABUNGAN) ══════════════════════════════════════════ --}}
{{-- ══ MODAL AUTOFILL NIP/NIK (GABUNGAN) ══════════════════════════════════════════ --}}
@if($kegiatan->is_registration_required)
<div id="modalAutofill"
     style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9500;
            display:flex;align-items:center;justify-content:center;padding:1rem;">
  <div class="modal-sekolah-card" role="dialog" aria-modal="true"
       aria-labelledby="modalAutofillTitle" style="max-width:480px;width:100%;">

    <div class="modal-sekolah-header">
      <div>
        <p style="font-size:11px;color:#9ca3af;margin:0 0 2px;text-transform:uppercase;letter-spacing:.06em;">Autofill Data</p>
        <h3 id="modalAutofillTitle" style="margin:0;font-size:1.05rem;">Cari Data Pendaftar</h3>
      </div>
      <button type="button" class="modal-sekolah-close" id="btnTutupAutofill">×</button>
    </div>

    <div style="padding:1rem 1.5rem 0;">
      <p style="font-size:.875rem;color:#6b7280;margin:0 0 1rem;line-height:1.5;">
        Masukkan NIP atau NIK untuk mengisi form secara otomatis.
        Data dicari dari riwayat pendaftaran dan API Dapodik.
      </p>

      {{-- Input gabungan --}}
      <div>
        <div style="display:flex;gap:8px;">
          <input type="text" id="autofillInput"
            placeholder="Masukkan NIP atau NIK..."
            maxlength="18"
            style="flex:1;padding:.6rem .85rem;border:1.5px solid #d0d7de;border-radius:8px;font-size:.9rem;">
          <button type="button" id="btnCariData"
            style="padding:.6rem 1.1rem;border:none;border-radius:8px;background:#3b5bdb;
                   color:#fff;font-size:.9rem;font-weight:600;cursor:pointer;">Cari</button>
        </div>
        <small style="display:block;margin-top:.5rem;color:#9ca3af;font-size:.75rem;">
          Masukkan NIP (18 digit) atau NIK (16 digit)
        </small>
      </div>

      {{-- Status box --}}
      <div id="autofillStatus" style="margin-top:.85rem;"></div>
    </div>

    <div style="padding:1rem 1.5rem 1.25rem;display:flex;gap:8px;justify-content:flex-end;
                border-top:1px solid #f3f4f6;margin-top:1rem;">
      <button type="button" id="btnSkipAutofill"
        style="padding:.55rem 1.1rem;border:1px solid #d0d7de;border-radius:8px;
               background:transparent;color:#6b7280;font-size:.9rem;cursor:pointer;">
        Isi Manual
      </button>
      <button type="button" id="btnGunakanData"
        style="display:none;padding:.55rem 1.1rem;border:none;border-radius:8px;
               background:#16a34a;color:#fff;font-size:.9rem;font-weight:600;cursor:pointer;">
        Gunakan Data Ini
      </button>
    </div>

  </div>
</div>
@endif
{{-- ══ / MODAL AUTOFILL NIP/NIK ═══════════════════════════════════════════════════ --}}

<style>
  .autofill-tab {
    flex:1;padding:.5rem;border-radius:8px;border:1.5px solid #d0d7de;
    background:transparent;color:#6b7280;font-size:.9rem;font-weight:500;cursor:pointer;
  }
  .autofill-tab.active { border-color:#3b5bdb;background:#eef2ff;color:#1e40af; }
  .af-box {
    padding:.65rem .85rem;border-radius:8px;font-size:.85rem;line-height:1.5;margin-top:.5rem;
  }
  .af-success { background:#f0fdf4;color:#166534;border:1px solid #bbf7d0; }
  .af-error   { background:#fef2f2;color:#991b1b;border:1px solid #fecaca; }
  .af-loading { background:#f9fafb;color:#6b7280;border:1px solid #e5e7eb; }
  .af-manual  { background:#fffbeb;color:#92400e;border:1px solid #fde68a; }
</style>
{{-- ══ / MODAL AUTOFILL NIP/NIK ════════════════════════════════════════════ --}}

<style>
  .autofill-tab {
    flex: 1;
    padding: .5rem;
    border-radius: 8px;
    border: 1.5px solid #d0d7de;
    background: transparent;
    color: #6b7280;
    font-size: .9rem;
    font-weight: 500;
    cursor: pointer;
    transition: all .15s;
  }
  .autofill-tab.active {
    border-color: #3b5bdb;
    background: #eef2ff;
    color: #1e40af;
  }
  .autofill-alert {
    display: flex;
    align-items: flex-start;
    gap: .5rem;
    padding: .65rem .85rem;
    border-radius: 8px;
    font-size: .85rem;
    line-height: 1.4;
  }
  .autofill-alert.info    { background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; }
  .autofill-alert.success { background:#f0fdf4; color:#166534; border:1px solid #bbf7d0; }
  .autofill-alert.error   { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }
  .autofill-alert.loading { background:#f9fafb; color:#6b7280; border:1px solid #e5e7eb; }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {

    // ── Elemen utama ──────────────────────────────────────────────────────────
    const radioSekolah      = document.getElementById('radioSekolah');
    const radioManual       = document.getElementById('radioManual');
    const panelPilihSekolah = document.getElementById('panelPilihSekolah');
    const panelManual       = document.getElementById('panelInstansiManual');
    const inputManual       = document.getElementById('inputInstansiManual');
    const hiddenSekolahId   = document.getElementById('sekolah_id');
    const hiddenInstansi    = document.getElementById('Instansi');

    const sekolahDisplay    = document.getElementById('sekolahTerpilihDisplay');
    const sekolahNama       = document.getElementById('sekolahTerpilihNama');

    const btnBukaModal      = document.getElementById('btnBukaModal');
    const btnTutupModal     = document.getElementById('btnTutupModal');
    const modalSekolah      = document.getElementById('modalSekolah');
    const modalInputCari    = document.getElementById('modalInputCari');
    const modalKotaFilter   = document.getElementById('modalKotaFilter');
    const modalHasil        = document.getElementById('modalHasil');

    const selectKota        = document.getElementById('kota_id');

    if (!radioSekolah) return; // halaman tanpa form pendaftaran

    // ── Fungsi tampil panel sesuai radio ─────────────────────────────────────
    function applyRadio() {
      const modeSekolah = radioSekolah.checked;
      panelPilihSekolah.hidden = !modeSekolah;
      panelManual.hidden       =  modeSekolah;

      if (!modeSekolah) {
        // Pindah ke manual → kosongkan pilihan sekolah
        hiddenSekolahId.value = '';
        resetTampilSekolah();
      } else {
        // Pindah ke sekolah → kosongkan input manual
        inputManual.value    = '';
        hiddenInstansi.value = '';
      }
    }

    radioSekolah.addEventListener('change', applyRadio);
    radioManual.addEventListener('change', applyRadio);
    applyRadio(); // init

    // Sinkron input manual ke hidden
    inputManual.addEventListener('input', function () {
      hiddenInstansi.value = inputManual.value.trim();
    });

    // ── Tampil / reset sekolah terpilih di kotak display ─────────────────────
    function tampilSekolahTerpilih(sekolah) {
      sekolahNama.textContent  = sekolah.nama_sekolah + ' — ' + sekolah.kab_kota;
      sekolahNama.classList.remove('sekolah-placeholder');
      sekolahDisplay.classList.add('has-value');
    }

    function resetTampilSekolah() {
      sekolahNama.textContent = 'Belum ada sekolah dipilih';
      sekolahNama.classList.add('sekolah-placeholder');
      sekolahDisplay.classList.remove('has-value');
    }

    // ── Buka / tutup modal ────────────────────────────────────────────────────
    function bukaModal() {
      // Sinkronkan filter kota dari form ke modal
      if (selectKota && modalKotaFilter) {
        modalKotaFilter.value = selectKota.value || '';
      }
      modalSekolah.hidden = false;
      document.body.style.overflow = 'hidden';
      setTimeout(function () { modalInputCari.focus(); }, 100);
    }

    function tutupModal() {
      modalSekolah.hidden = true;
      document.body.style.overflow = '';
      modalInputCari.value  = '';
      modalHasil.innerHTML  = '<div class="modal-sekolah-empty">Ketik untuk mulai mencari sekolah.</div>';
    }

    btnBukaModal.addEventListener('click', bukaModal);
    btnTutupModal.addEventListener('click', tutupModal);

    // Klik backdrop untuk tutup
    modalSekolah.addEventListener('click', function (e) {
      if (e.target === modalSekolah) tutupModal();
    });

    // Escape untuk tutup
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !modalSekolah.hidden) tutupModal();
    });

    // ── Pencarian sekolah di dalam modal ─────────────────────────────────────
    let searchTimer = null;

    function doSearch() {
      const keyword = modalInputCari.value.trim();
      const kotaId  = modalKotaFilter ? modalKotaFilter.value : '';

      if (keyword.length < 3) {
        modalHasil.innerHTML = '<div class="modal-sekolah-empty">Minimal 3 karakter untuk mulai mencari.</div>';
        return;
      }

      modalHasil.innerHTML = '<div class="modal-sekolah-empty">Mencari...</div>';

      fetch('/pendaftaran/cari-sekolah?q=' + encodeURIComponent(keyword) + '&kota_id=' + encodeURIComponent(kotaId))
        .then(function (res) { return res.json(); })
        .then(function (data) {
          modalHasil.innerHTML = '';

          if (!data.length) {
            modalHasil.innerHTML = '<div class="modal-sekolah-empty">Sekolah tidak ditemukan. Coba kata kunci lain atau gunakan Input Manual.</div>';
            return;
          }

          data.forEach(function (sekolah) {
            const item = document.createElement('div');
            item.className = 'modal-sekolah-item';
            item.innerHTML =
              '<strong>' + sekolah.nama_sekolah + '</strong>' +
              '<small>' + sekolah.npsn + ' &bull; ' + sekolah.kab_kota + ' &bull; ' + sekolah.jenjang + '</small>';

            item.addEventListener('click', function () {
              // Simpan ke hidden fields
              hiddenSekolahId.value = sekolah.sekolah_id;
              hiddenInstansi.value  = sekolah.nama_sekolah;

              // Tampilkan di kotak display
              tampilSekolahTerpilih(sekolah);

              // Tutup modal
              tutupModal();
            });

            modalHasil.appendChild(item);
          });
        })
        .catch(function () {
          modalHasil.innerHTML = '<div class="modal-sekolah-empty">Gagal mencari sekolah. Periksa koneksi Anda.</div>';
        });
    }

    modalInputCari.addEventListener('input', function () {
      clearTimeout(searchTimer);
      searchTimer = setTimeout(doSearch, 350);
    });

    modalKotaFilter.addEventListener('change', function () {
      if (modalInputCari.value.trim().length >= 3) doSearch();
    });

    // ── Validasi form sebelum submit ──────────────────────────────────────────
    document.getElementById('formPendaftaran').addEventListener('submit', function (e) {
      if (radioSekolah.checked) {
        if (!hiddenSekolahId.value) {
          e.preventDefault();
          alert('Pilih sekolah terlebih dahulu dengan menekan tombol "Cari Sekolah".');
          btnBukaModal.focus();
        }
      } else {
        if (!inputManual.value.trim()) {
          e.preventDefault();
          alert('Isi nama instansi terlebih dahulu.');
          inputManual.focus();
        }
      }
    });

  });
</script>

<script>
/* ── SCRIPT 2: Modal Autofill NIP/NIK (GABUNGAN) ──────────────────────────── */
(function () {
  var formEl = document.getElementById('formPendaftaran');
  if (!formEl) return;

  var modal = document.getElementById('modalAutofill');
  var statusBox = document.getElementById('autofillStatus');
  var btnGunakan = document.getElementById('btnGunakanData');
  var autofillInput = document.getElementById('autofillInput');
  var btnCariData = document.getElementById('btnCariData');

  var _data = null, _sekolah = null;

  // Modal sudah display:flex dari HTML, body lock
  document.body.style.overflow = 'hidden';

  // ── Tutup ──────────────────────────────────────────────────────────────
  function tutupAutofill() {
    modal.style.display = 'none';
    document.body.style.overflow = '';
  }
  document.getElementById('btnTutupAutofill').addEventListener('click', tutupAutofill);
  document.getElementById('btnSkipAutofill').addEventListener('click', tutupAutofill);
  modal.addEventListener('click', function (e) { if (e.target === modal) tutupAutofill(); });

  // ── Status ─────────────────────────────────────────────────────────────
  function setStatus(cls, msg) {
    statusBox.innerHTML = '<div class="autofill-alert ' + cls + '">' + msg + '</div>';
  }

  // ── Deteksi apakah input adalah NIP atau NIK ───────────────────────────
  function detectSearchType(input) {
    var cleaned = input.replace(/\s/g, '');
    if (/^\d{16}$/.test(cleaned)) {
      return { type: 'nik', value: cleaned };
    } else if (/^\d{18}$/.test(cleaned)) {
      return { type: 'nip', value: cleaned };
    } else if (cleaned.length >= 10) {
      // Jika panjang antara 10-18 digit, coba cek sebagai NIP dulu
      return { type: 'nip', value: cleaned };
    }
    return null;
  }

  // ── Fetch berdasarkan NIP atau NIK ─────────────────────────────────────
  function doFetch(url) {
    _data = null;
    _sekolah = null;
    btnGunakan.style.display = 'none';
    setStatus('loading', '⏳ Mencari data...');

    fetch(url)
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (!res.success) {
          setStatus('error',
            '❌ ' + (res.message || 'Data tidak ditemukan.') +
            '<br><small style="margin-top:4px;display:block;">Tutup modal ini dan isi form secara manual.</small>'
          );
          return;
        }
        _data = res.data;
        _sekolah = res.sekolah || null;
        var src = { peserta_lokal: 'riwayat pendaftaran', ptk_lokal: 'lokal', dapodik: 'API Dapodik' };
        var nama = (_data && _data.nama) ? ' &mdash; <strong>' + _data.nama + '</strong>' : '';
        setStatus('success', '✅ Ditemukan dari ' + (src[res.source] || res.source) + nama);
        btnGunakan.style.display = 'inline-block';
      })
      .catch(function () {
        setStatus('error', '❌ Gagal menghubungi server. Periksa koneksi Anda.');
      });
  }

  // ── Cari data berdasarkan input ────────────────────────────────────────
  function cariData() {
    var inputValue = autofillInput.value.trim();
    if (!inputValue) {
      setStatus('error', '❌ Masukkan NIP atau NIK terlebih dahulu.');
      return;
    }

    var detected = detectSearchType(inputValue);
    if (!detected) {
      setStatus('error', '❌ Format tidak valid. Masukkan NIP (18 digit) atau NIK (16 digit).');
      return;
    }

    if (detected.type === 'nip') {
      doFetch('/pendaftaran/ptk/cek-nip?nip=' + encodeURIComponent(detected.value));
    } else {
      doFetch('/pendaftaran/ptk/cek-nik?nik=' + encodeURIComponent(detected.value));
    }
  }

  btnCariData.addEventListener('click', cariData);
  autofillInput.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') cariData();
  });

  // ── Fungsi untuk memilih option select berdasarkan value ─────────────────
  function setSelectValue(selectId, value) {
    var select = document.getElementById(selectId);
    if (!select || value == null || value === '') return false;
    for (var i = 0; i < select.options.length; i++) {
      if (String(select.options[i].value) === String(value)) {
        select.selectedIndex = i;
        select.dispatchEvent(new Event('change', { bubbles: true }));
        return true;
      }
    }
    return false;
  }

  // ── Fungsi untuk mengisi sekolah dari data ─────────────────────────────
  function setSekolahFromData(sekolahData) {
    if (!sekolahData) return false;

    var radioSekolah = document.getElementById('radioSekolah');
    var hiddenSekolahId = document.getElementById('sekolah_id');
    var hiddenInstansi = document.getElementById('Instansi');
    var sekolahDisplay = document.getElementById('sekolahTerpilihNama');
    var sekolahDisplayContainer = document.getElementById('sekolahTerpilihDisplay');

    if (!radioSekolah) return false;

    // Pilih mode sekolah
    radioSekolah.checked = true;
    radioSekolah.dispatchEvent(new Event('change', { bubbles: true }));

    if (sekolahData.sekolah_id && hiddenSekolahId) {
      hiddenSekolahId.value = sekolahData.sekolah_id;
    }

    if (hiddenInstansi) {
      hiddenInstansi.value = sekolahData.nama_sekolah || '';
    }

    // Update tampilan
    if (sekolahDisplay) {
      var displayText = sekolahData.nama_sekolah;
      if (sekolahData.kab_kota) displayText += ' — ' + sekolahData.kab_kota;
      sekolahDisplay.textContent = displayText;
      sekolahDisplay.classList.remove('sekolah-placeholder');
      if (sekolahDisplayContainer) sekolahDisplayContainer.classList.add('has-value');
    }

    return true;
  }

  // ── Gunakan Data ───────────────────────────────────────────────────────
btnGunakan.addEventListener('click', function () {
    if (!_data) return;

    function setVal(id, val) {
        var el = document.getElementById(id);
        if (el && val != null && val !== '') {
            el.value = val;
            el.dispatchEvent(new Event('change', { bubbles: true }));
            el.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }

    function setSelectValue(selectId, value) {
        var select = document.getElementById(selectId);
        if (!select || value == null || value === '') return false;
        for (var i = 0; i < select.options.length; i++) {
            if (String(select.options[i].value) === String(value)) {
                select.selectedIndex = i;
                select.dispatchEvent(new Event('change', { bubbles: true }));
                return true;
            }
        }
        return false;
    }

    // Data personal
    setVal('nama', _data.nama);
    setVal('nip', _data.nip);
    setVal('nik', _data.nik);
    setVal('email', _data.email);
    setVal('tanggal_lahir', _data.tanggal_lahir);

    // Tempat lahir (jika ada)
    if (_data.tempat_lahir) {
        setVal('tempat_lahir', _data.tempat_lahir);
    }

    // Jenis kelamin
    if (_data.jenis_kelamin) {
        setSelectValue('jenis_kelamin', _data.jenis_kelamin);
    }

    // KOTA: isi berdasarkan kota_id dari API
    if (_data.kota_id) {
        setSelectValue('kota_id', _data.kota_id);
    } else if (_data.kota_nama) {
        var kotaSelect = document.getElementById('kota_id');
        if (kotaSelect) {
            for (var i = 0; i < kotaSelect.options.length; i++) {
                var optionText = kotaSelect.options[i].text.toLowerCase();
                var kotaNama = _data.kota_nama.toLowerCase();
                if (optionText.includes(kotaNama) || kotaNama.includes(optionText)) {
                    kotaSelect.selectedIndex = i;
                    kotaSelect.dispatchEvent(new Event('change', { bubbles: true }));
                    break;
                }
            }
        }
    }

    // ========== PERBAIKAN: SEKOLAH / INSTANSI ==========
    var radioSekolah = document.getElementById('radioSekolah');
    var radioManual = document.getElementById('radioManual');
    var hiddenSekolahId = document.getElementById('sekolah_id');
    var hiddenInstansi = document.getElementById('Instansi');
    var inputManualInstansi = document.getElementById('inputInstansiManual');
    var sekolahDisplay = document.getElementById('sekolahTerpilihNama');
    var sekolahDisplayContainer = document.getElementById('sekolahTerpilihDisplay');

    // CEK: Apakah ada data sekolah dari API
    if (_sekolah && _sekolah.nama_sekolah && _sekolah.nama_sekolah.length > 2) {
        // MODE: Pilih dari Daftar Sekolah
        if (radioSekolah) {
            radioSekolah.checked = true;
            radioSekolah.dispatchEvent(new Event('change', { bubbles: true }));
        }

        if (hiddenSekolahId && _sekolah.sekolah_id) {
            hiddenSekolahId.value = _sekolah.sekolah_id;
        }

        if (hiddenInstansi) {
            hiddenInstansi.value = _sekolah.nama_sekolah;
        }

        // Update tampilan display
        if (sekolahDisplay) {
            var displayText = _sekolah.nama_sekolah;
            if (_sekolah.kab_kota) displayText += ' — ' + _sekolah.kab_kota;
            sekolahDisplay.textContent = displayText;
            sekolahDisplay.classList.remove('sekolah-placeholder');
            if (sekolahDisplayContainer) sekolahDisplayContainer.classList.add('has-value');
        }

        // Kosongkan input manual
        if (inputManualInstansi) inputManualInstansi.value = '';
    }
    // CEK: Apakah ada data instansi dari data PTK (untuk mode manual)
    else if (_data.instansi_nama && _data.instansi_nama.length > 2) {
        // MODE: Input Manual
        if (radioManual) {
            radioManual.checked = true;
            radioManual.dispatchEvent(new Event('change', { bubbles: true }));
        }

        // Isi input manual
        if (inputManualInstansi) {
            inputManualInstansi.value = _data.instansi_nama;
            inputManualInstansi.dispatchEvent(new Event('input', { bubbles: true }));
        }

        if (hiddenInstansi) {
            hiddenInstansi.value = _data.instansi_nama;
        }

        // Kosongkan hidden sekolah_id dan tampilan display
        if (hiddenSekolahId) hiddenSekolahId.value = '';
        if (sekolahDisplay) {
            sekolahDisplay.textContent = 'Belum ada sekolah dipilih';
            sekolahDisplay.classList.add('sekolah-placeholder');
            if (sekolahDisplayContainer) sekolahDisplayContainer.classList.remove('has-value');
        }
    }
    // FALLBACK: Cek apakah ada nama instansi langsung dari data
    else if (_data.instansi && _data.instansi.length > 2) {
        // MODE: Input Manual
        if (radioManual) {
            radioManual.checked = true;
            radioManual.dispatchEvent(new Event('change', { bubbles: true }));
        }

        if (inputManualInstansi) {
            inputManualInstansi.value = _data.instansi;
            inputManualInstansi.dispatchEvent(new Event('input', { bubbles: true }));
        }

        if (hiddenInstansi) {
            hiddenInstansi.value = _data.instansi;
        }

        if (hiddenSekolahId) hiddenSekolahId.value = '';
        if (sekolahDisplay) {
            sekolahDisplay.textContent = 'Belum ada sekolah dipilih';
            sekolahDisplay.classList.add('sekolah-placeholder');
            if (sekolahDisplayContainer) sekolahDisplayContainer.classList.remove('has-value');
        }
    }
    // FALLBACK TERAKHIR: Cek apakah ada nama_sekolah dari data (tanpa sekolah_id)
    else if (_data.nama_sekolah && _data.nama_sekolah.length > 2 && (!_sekolah || !_sekolah.sekolah_id)) {
        // MODE: Input Manual (karena tidak ada sekolah_id yang valid)
        if (radioManual) {
            radioManual.checked = true;
            radioManual.dispatchEvent(new Event('change', { bubbles: true }));
        }

        if (inputManualInstansi) {
            inputManualInstansi.value = _data.nama_sekolah;
            inputManualInstansi.dispatchEvent(new Event('input', { bubbles: true }));
        }

        if (hiddenInstansi) {
            hiddenInstansi.value = _data.nama_sekolah;
        }

        if (hiddenSekolahId) hiddenSekolahId.value = '';
        if (sekolahDisplay) {
            sekolahDisplay.textContent = 'Belum ada sekolah dipilih';
            sekolahDisplay.classList.add('sekolah-placeholder');
            if (sekolahDisplayContainer) sekolahDisplayContainer.classList.remove('has-value');
        }
    }

    tutupAutofill();
});

  // Fokus ke input saat modal terbuka
  setTimeout(function () {
    autofillInput.focus();
  }, 100);

})();
</script>

@endsection

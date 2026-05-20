@php
  $user = Auth::user();
  $kegiatanUser = session('auth_peserta'); // Session dari AuthKegiatanController

  $isAdmin = $user && in_array($user->role_id, [1, 2]); // 1=Administrator, 2=Supervisor
  $isKegiatanLogin = !empty($kegiatanUser); // Check kegiatan login via session

  // Tentukan brand URL berdasarkan jenis login
  if ($isKegiatanLogin) {
    $brandUrl = route('kegiatan.dashboard', $kegiatanUser['kegiatan_slug']);
  } elseif ($isAdmin) {
    $brandUrl = route('admin.dashboard');
  } else {
    $brandUrl = url('/dashboard');
  }
@endphp

<header class="site-header {{ Auth::check() || $isKegiatanLogin ? 'header-auth' : 'header-guest' }}">
  <div class="container header-inner">

    <a href="{{ $brandUrl }}" class="brand">
      <div class="brand-logo">M</div>

      <div class="brand-text">
        <h1>MOOC BGTK Banten</h1>
        <p>Platform Pembelajaran Digital</p>
      </div>
    </a>

    <button
      type="button"
      class="header-menu-toggle"
      id="headerMenuToggle"
      aria-label="Buka menu"
      aria-expanded="false"
      aria-controls="mainNavMenu"
    >
      <span></span>
      <span></span>
      <span></span>
    </button>

    <nav class="nav-menu" id="mainNavMenu">
      {{-- ========== KEGIATAN LOGIN (Session-based) ========== --}}
      @if($isKegiatanLogin)
        <a href="{{ route('kegiatan.dashboard', $kegiatanUser['kegiatan_slug']) }}">
          Dashboard Kegiatan
        </a>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
         <a href="{{ route('kegiatan.kelas.index', ['slug' => $kegiatanUser['kegiatan_slug']]) }}">
        Kelas Saya
         </a>
        <a href="{{ route('sertifikat.index') }}">Sertifikat</a>
        <a href="{{ route('bantuan.index') }}">Bantuan</a>
        {{-- <span class="nav-separator">•</span>
        <span class="nav-info-text">
          📚 {{ $kegiatanUser['kegiatan_nama'] }}
        </span> --}}

      {{-- ========== ADMIN LOGIN ========== --}}
      @elseif($isAdmin)
        <a href="{{ route('admin.dashboard') }}">Dashboard Admin</a>
        <a href="{{ route('admin.kegiatan.index') }}">Kegiatan</a>
        <a href="{{ route('admin.users.index') }}">Kelola User</a>
        <a href="{{ url('/dashboard') }}">Lihat Web</a>

      {{-- ========== REGULAR USER LOGIN ========== --}}
      @elseif(Auth::check())
        <a href="{{ url('/dashboard') }}">Dashboard</a>
         <a href="{{ route('kegiatan.kelas.index', ['slug' => $kegiatanUser['kegiatan_slug']]) }}">
        Kelas Saya
         </a>
        <a href="{{ route('sertifikat.index') }}">Sertifikat</a>
        <a href="{{ route('bantuan.index') }}">Bantuan</a>

      {{-- ========== GUEST (Belum Login) ========== --}}
      @else
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        {{-- <a href="#">Kelas</a> --}}
        <a href="{{ route('sertifikat.index') }}">Sertifikat</a>
        <a href="{{ route('bantuan.index') }}">Bantuan</a>
      @endif
    </nav>

    <div class="header-actions">
      {{-- ========== KEGIATAN LOGIN (Session-based) ========== --}}
      @if($isKegiatanLogin)
        <details class="user-dropdown">
          <summary class="baduy-user-trigger">
            <span class="user-trigger-name">
               {{ \Illuminate\Support\Str::limit($kegiatanUser['nama'], 20) }}
            </span>

            <span class="user-trigger-arrow">▾</span>
          </summary>

          <div class="user-dropdown-menu">
            <div class="user-dropdown-info">
              <strong>{{ $kegiatanUser['nama'] }}</strong>
              <small>{{ $kegiatanUser['email'] }}</small>
              <small style="display: block; margin-top: 5px; color: #0d56b6; font-weight: 600;">
                📚 {{ $kegiatanUser['kegiatan_nama'] }}
              </small>
            </div>

            <form action="{{ route('kegiatan.logout', ['slug' => $kegiatanUser['kegiatan_slug']]) }}" method="POST">
              @csrf

              <button type="submit" class="dropdown-logout-btn">
                Logout Kegiatan
              </button>
            </form>
          </div>
        </details>

      {{-- ========== ADMIN/REGULAR USER LOGIN ========== --}}
      @elseif(Auth::check())
        <details class="user-dropdown">
          <summary class="baduy-user-trigger">
            <span class="user-trigger-name">
              {{ $user->name ?: $user->email }}
            </span>

            <span class="user-trigger-arrow">▾</span>
          </summary>

          <div class="user-dropdown-menu">
            <div class="user-dropdown-info">
              <strong>{{ $user->nama ?: 'User' }}</strong>
              <small>{{ $user->email }}</small>
              @if($isAdmin)
                <small style="display: block; margin-top: 5px; color: #0d56b6; font-weight: 600;">
                  Admin
                </small>
              @endif
            </div>

            <form action="{{ route('logout') }}" method="POST">
              @csrf

              <button type="submit" class="dropdown-logout-btn">
                Logout
              </button>
            </form>
          </div>
        </details>

      {{-- ========== GUEST (Belum Login) ========== --}}
      @else
        <a href="{{ route('login') }}" class="login-btn baduy-user-trigger">
          Login Admin
        </a>
      @endif
    </div>

  </div>
</header>

<style>
  /* Support untuk nav-info-text di kegiatan login */
  .nav-info-text {
    display: inline-block;
    padding: 0 10px;
    color: #666;
    font-size: 0.9rem;
    white-space: nowrap;
  }

  .nav-separator {
    color: #ddd;
    margin: 0 5px;
  }

  @media (max-width: 768px) {
    .nav-info-text {
      display: none;
    }

    .nav-separator {
      display: none;
    }
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const toggleButton = document.getElementById('headerMenuToggle');
    const navMenu = document.getElementById('mainNavMenu');

    if (!toggleButton || !navMenu) {
      return;
    }

    function closeMenu() {
      navMenu.classList.remove('is-open');
      toggleButton.classList.remove('is-open');
      toggleButton.setAttribute('aria-expanded', 'false');
      toggleButton.setAttribute('aria-label', 'Buka menu');
    }

    function toggleMenu() {
      const isOpen = navMenu.classList.toggle('is-open');

      toggleButton.classList.toggle('is-open', isOpen);
      toggleButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      toggleButton.setAttribute('aria-label', isOpen ? 'Tutup menu' : 'Buka menu');
    }

    toggleButton.addEventListener('click', function (event) {
      event.stopPropagation();
      toggleMenu();
    });

    navMenu.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', closeMenu);
    });

    document.addEventListener('click', function (event) {
      const clickedInsideNav = navMenu.contains(event.target);
      const clickedToggle = toggleButton.contains(event.target);

      if (!clickedInsideNav && !clickedToggle) {
        closeMenu();
      }
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closeMenu();
      }
    });
  });
</script>

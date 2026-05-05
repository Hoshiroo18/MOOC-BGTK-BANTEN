@php
  $user = Auth::user();

  $adminEmails = [
    'yusup.ardabili@kemendikdasmen.go.id',
  ];

  $isAdmin = $user && (
    ($user->role ?? null) === 'admin' ||
    in_array($user->email, $adminEmails, true)
  );

  $brandUrl = $isAdmin
    ? route('admin.dashboard')
    : url('/dashboard');
@endphp

<header class="site-header {{ Auth::check() ? 'header-auth' : 'header-guest' }}">
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
      @auth
        @if($isAdmin)
          <a href="{{ route('admin.dashboard') }}">Dashboard Admin</a>
          <a href="{{ route('admin.kegiatan.index') }}">Kegiatan</a>
          <a href="{{ route('admin.users.index') }}">Kelola User</a>
          <a href="{{ url('/dashboard') }}">Lihat Web</a>
        @else
          <a href="{{ url('/dashboard') }}">Dashboard</a>
          <a href="{{ route('kelas.index') }}">Kelas Saya</a>
          <a href="{{ route('sertifikat.index') }}">Sertifikat</a>
          <a href="{{ route('bantuan.index') }}">Bantuan</a>
        @endif
      @else
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <a href="{{ route('kelas.index') }}">Kelas</a>
        <a href="{{ route('sertifikat.index') }}">Sertifikat</a>
        <a href="{{ route('bantuan.index') }}">Bantuan</a>
      @endauth
    </nav>

    <div class="header-actions">
      @auth
        <details class="user-dropdown">
          <summary class="baduy-user-trigger">
            <span class="user-trigger-name">
              {{ $user->name ?: $user->email }}
            </span>

            <span class="user-trigger-arrow">▾</span>
          </summary>

          <div class="user-dropdown-menu">
            <div class="user-dropdown-info">
              <strong>{{ $user->name ?: 'User' }}</strong>
              <small>{{ $user->email }}</small>
            </div>

            <form action="{{ route('logout') }}" method="POST">
              @csrf

              <button type="submit" class="dropdown-logout-btn">
                Logout
              </button>
            </form>
          </div>
        </details>
      @else
        <a href="{{ route('login') }}" class="login-btn baduy-user-trigger">
          Login
        </a>
      @endauth
    </div>

  </div>
</header>

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
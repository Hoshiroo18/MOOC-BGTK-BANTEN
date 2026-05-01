<header class="site-header {{ Auth::check() ? 'header-auth' : 'header-guest' }}">
  <div class="container header-inner">

    @auth
      <a href="{{ route('admin.dashboard') }}" class="brand">
    @else
      <a href="{{ url('/dashboard') }}" class="brand">
    @endauth
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
        <a href="{{ route('admin.dashboard') }}">Dashboard Admin</a>
        <a href="{{ route('admin.kegiatan.index') }}">Kegiatan</a>
        <a href="{{ url('/admin/users') }}">Kelola User</a>
        <a href="{{ url('/dashboard') }}">Lihat Web</a>
      @else
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <a href="{{ url('/kelas') }}">Kelas</a>
        <a href="{{ url('/sertifikat') }}">Sertifikat</a>
        <a href="{{ url('/bantuan') }}">Bantuan</a>
      @endauth
    </nav>

    <div class="header-actions">
      @auth
        <details class="user-dropdown">
          <summary class="user-trigger baduy-user-trigger">
            <span class="user-trigger-name">
              {{ Auth::user()->name ?: Auth::user()->email }}
            </span>

            <span class="user-trigger-arrow">▾</span>
          </summary>

          <div class="user-dropdown-menu">
            <div class="user-dropdown-info">
              <strong>{{ Auth::user()->name ?: 'User' }}</strong>
              <small>{{ Auth::user()->email }}</small>
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

    toggleButton.addEventListener('click', function () {
      const isOpen = navMenu.classList.toggle('is-open');

      toggleButton.classList.toggle('is-open', isOpen);
      toggleButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      toggleButton.setAttribute('aria-label', isOpen ? 'Tutup menu' : 'Buka menu');
    });

    navMenu.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        navMenu.classList.remove('is-open');
        toggleButton.classList.remove('is-open');
        toggleButton.setAttribute('aria-expanded', 'false');
        toggleButton.setAttribute('aria-label', 'Buka menu');
      });
    });

    document.addEventListener('click', function (event) {
      const clickedInsideNav = navMenu.contains(event.target);
      const clickedToggle = toggleButton.contains(event.target);

      if (!clickedInsideNav && !clickedToggle) {
        navMenu.classList.remove('is-open');
        toggleButton.classList.remove('is-open');
        toggleButton.setAttribute('aria-expanded', 'false');
        toggleButton.setAttribute('aria-label', 'Buka menu');
      }
    });
  });
</script>
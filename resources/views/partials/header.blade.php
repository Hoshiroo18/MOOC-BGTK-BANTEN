<header class="site-header">
  <div class="container header-inner">

    @auth
      <a href="{{ route('admin.dashboard') }}" class="brand">
    @else
      <a href="{{ url('/dashboard') }}" class="brand">
    @endauth
        <div class="brand-logo">M</div>
        <div>
          <h1>MOOC BGTK Banten</h1>
          <p>Platform Pembelajaran Digital</p>
        </div>
      </a>

    <nav class="nav-menu">
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
          <summary class="user-trigger">
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
        <a href="{{ route('login') }}" class="login-btn">
          Login
        </a>
      @endauth
    </div>

  </div>
</header>
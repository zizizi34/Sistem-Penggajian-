<div class="sidebar-menu">
  <ul class="menu">
    <li class="sidebar-title">Menu</li>

    <li class="sidebar-item {{ request()->routeIs('administrators.dashboard') ? 'active' : '' }}">
      <a href="{{ Route::has('administrators.dashboard') ? route('administrators.dashboard') : '#' }}" class="sidebar-link">
        <i class="bi bi-grid-fill"></i>
        <span>Beranda</span>
      </a>
    </li>

    <li class="sidebar-title">Data Master</li>

    <li class="sidebar-item {{ request()->routeIs('administrators.departemen.*') ? 'active' : '' }}">
      <a href="{{ Route::has('administrators.departemen.index') ? route('administrators.departemen.index') : '#' }}" class="sidebar-link">
        <i class="bi bi-building"></i>
        <span>Departemen</span>
      </a>
    </li>

    <li class="sidebar-item {{ request()->routeIs('administrators.jabatan.*') ? 'active' : '' }}">
      <a href="{{ Route::has('administrators.jabatan.index') ? route('administrators.jabatan.index') : '#' }}" class="sidebar-link">
        <i class="bi bi-briefcase-fill"></i>
        <span>Jabatan</span>
      </a>
    </li>

    <li class="sidebar-item {{ request()->routeIs('administrators.tunjangan.*') ? 'active' : '' }}">
      <a href="{{ Route::has('administrators.tunjangan.index') ? route('administrators.tunjangan.index') : '#' }}" class="sidebar-link">
        <i class="bi bi-plus-circle-fill"></i>
        <span>Tunjangan</span>
      </a>
    </li>

    <li class="sidebar-item {{ request()->routeIs('administrators.potongan.*') ? 'active' : '' }}">
      <a href="{{ Route::has('administrators.potongan.index') ? route('administrators.potongan.index') : '#' }}" class="sidebar-link">
        <i class="bi bi-dash-circle-fill"></i>
        <span>Potongan</span>
      </a>
    </li>

    <li class="sidebar-item {{ request()->routeIs('administrators.ptkp-status.*') ? 'active' : '' }}">
      <a href="{{ Route::has('administrators.ptkp-status.index') ? route('administrators.ptkp-status.index') : '#' }}" class="sidebar-link">
        <i class="bi bi-percent"></i>
        <span>Status PTKP</span>
      </a>
    </li>

    <li class="sidebar-title">Penggajian</li>

    <li class="sidebar-item {{ request()->routeIs('administrators.pegawai.*') ? 'active' : '' }}">
      <a href="{{ Route::has('administrators.pegawai.index') ? route('administrators.pegawai.index') : '#' }}" class="sidebar-link">
        <i class="bi bi-people-fill"></i>
        <span>Pegawai</span>
      </a>
    </li>

    <li class="sidebar-item {{ request()->routeIs('administrators.penggajian.*') ? 'active' : '' }}">
      <a href="{{ Route::has('administrators.penggajian.index') ? route('administrators.penggajian.index') : '#' }}" class="sidebar-link">
        <i class="bi bi-file-earmark-text"></i>
        <span>Data Penggajian</span>
      </a>
    </li>

    <li class="sidebar-title">Manajemen Akun</li>

    <li class="sidebar-item {{ request()->routeIs('administrators.users.*') ? 'active' : '' }}">
      <a href="{{ Route::has('administrators.users.index') ? route('administrators.users.index') : '#' }}" class="sidebar-link">
        <i class="bi bi-person-badge-fill"></i>
        <span>Administrator</span>
      </a>
    </li>

    <li class="sidebar-item {{ request()->routeIs('administrators.officers.*') ? 'active' : '' }}">
      <a href="{{ Route::has('administrators.officers.index') ? route('administrators.officers.index') : '#' }}" class="sidebar-link">
        <i class="bi bi-person-badge-fill"></i>
        <span>Petugas</span>
      </a>
    </li>

    <li class="sidebar-item {{ request()->routeIs('administrators.profile-settings.*') ? 'active' : '' }}">
      <a href="{{ Route::has('administrators.profile-settings.index') ? route('administrators.profile-settings.index') : '#' }}" class="sidebar-link">
        <i class="bi bi-person-fill-gear"></i>
        <span>Pengaturan Profil</span>
      </a>
    </li>

    <li class="sidebar-title"></li>

    <li class="sidebar-item">
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <a href="{{ route('logout') }}" class="sidebar-link" id="logout">
          <i class="bi bi-box-arrow-right"></i>
          <span>Keluar</span>
        </a>
      </form>
    </li>
  </ul>
</div>
    </li>

    <li class="sidebar-title"></li>

    <li class="sidebar-item">
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <a href="{{ route('logout') }}" class="sidebar-link" id="logout">
          <i class="bi bi-box-arrow-right"></i>
          <span>Keluar</span>
        </a>
      </form>
    </li>
  </ul>
</div>

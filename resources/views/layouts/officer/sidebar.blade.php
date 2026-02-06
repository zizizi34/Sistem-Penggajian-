<div class="sidebar-menu">
  <ul class="menu">
    <li class="sidebar-title">Menu</li>

    <li class="sidebar-item {{ request()->routeIs('officers.dashboard') ? 'active' : '' }}">
      <a href="{{ route('officers.dashboard') }}" class="sidebar-link">
        <i class="bi bi-grid-fill"></i>
        <span>Beranda</span>
      </a>
    </li>

    <li class="sidebar-title">Data Master</li>

    <li class="sidebar-item {{ request()->routeIs('officers.departemen.*') ? 'active' : '' }}">
      <a href="{{ route('officers.departemen.index') }}" class="sidebar-link">
        <i class="bi bi-building"></i>
        <span>Departemen</span>
      </a>
    </li>

    <li class="sidebar-item {{ request()->routeIs('officers.jabatan.*') ? 'active' : '' }}">
      <a href="{{ route('officers.jabatan.index') }}" class="sidebar-link">
        <i class="bi bi-briefcase-fill"></i>
        <span>Jabatan</span>
      </a>
    </li>

    <li class="sidebar-item {{ request()->routeIs('officers.tunjangan.*') ? 'active' : '' }}">
      <a href="{{ route('officers.tunjangan.index') }}" class="sidebar-link">
        <i class="bi bi-plus-circle-fill"></i>
        <span>Tunjangan</span>
      </a>
    </li>

    <li class="sidebar-item {{ request()->routeIs('officers.potongan.*') ? 'active' : '' }}">
      <a href="{{ route('officers.potongan.index') }}" class="sidebar-link">
        <i class="bi bi-dash-circle-fill"></i>
        <span>Potongan</span>
      </a>
    </li>

    <li class="sidebar-title">Penggajian</li>

    <li class="sidebar-item {{ request()->routeIs('officers.pegawai.*') ? 'active' : '' }}">
      <a href="{{ route('officers.pegawai.index') }}" class="sidebar-link">
        <i class="bi bi-people-fill"></i>
        <span>Pegawai</span>
      </a>
    </li>

    <li class="sidebar-item {{ request()->routeIs('officers.penggajian.*') ? 'active' : '' }}">
      <a href="{{ route('officers.penggajian.index') }}" class="sidebar-link">
        <i class="bi bi-file-earmark-text"></i>
        <span>Data Penggajian</span>
      </a>
    </li>

    <li class="sidebar-item {{ request()->routeIs('officers.profile-settings.*') ? 'active' : '' }}">
      <a href="{{ route('officers.profile-settings.index') }}" class="sidebar-link">
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

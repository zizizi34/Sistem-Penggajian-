<div class="sidebar-menu">
  <ul class="menu">
    <li class="sidebar-title">Menu Pegawai</li>

    <li class="sidebar-item {{ request()->routeIs('students.dashboard') ? 'active' : '' }}">
      <a href="{{ route('students.dashboard') }}" class="sidebar-link">
        <i class="bi bi-grid-fill"></i>
        <span>Beranda</span>
      </a>
    </li>

    <li class="sidebar-title">Informasi Pribadi</li>

    <li class="sidebar-item {{ request()->routeIs('students.attendance.*') ? 'active' : '' }}">
      <a href="{{ route('students.attendance.index') }}" class="sidebar-link">
        <i class="bi bi-calendar-check"></i>
        <span>Absensi Saya</span>
      </a>
    </li>

    <li class="sidebar-item {{ request()->routeIs('students.payroll.*') ? 'active' : '' }}">
      <a href="{{ route('students.payroll.index') }}" class="sidebar-link">
        <i class="bi bi-file-earmark-text"></i>
        <span>Slip Gaji</span>
      </a>
    </li>

    <li class="sidebar-item {{ request()->routeIs('students.profile-settings.*') ? 'active' : '' }}">
      <a href="{{ route('students.profile-settings.index') }}" class="sidebar-link">
        <i class="bi bi-person-fill-gear"></i>
        <span>Pengaturan Profil</span>
      </a>
    </li>

    <li class="sidebar-title"></li>

    <li class="sidebar-item">
      <form action="{{ route('logout') }}" method="POST" id="logout-form">
        @csrf
      </form>
      <a href="javascript:void(0)" class="sidebar-link" onclick="document.getElementById('logout-form').submit();">
        <i class="bi bi-box-arrow-right"></i>
        <span>Keluar</span>
      </a>
    </li>
  </ul>
</div>

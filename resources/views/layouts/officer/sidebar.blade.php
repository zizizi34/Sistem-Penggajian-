<div class="sidebar-menu">
  <ul class="menu">
    <li class="sidebar-title">Menu</li>

    {{-- Render menu dinamis berdasarkan permission dari controller --}}
    @if(isset($menuStructure) && count($menuStructure) > 0)
      @php $lastGroup = null; @endphp
      
      @foreach($menuStructure as $menu)
        {{-- Render section title hanya jika berbeda dari sebelumnya --}}
        @if($menu['title'] !== 'Dashboard' && $menu['title'] !== 'Profile')
          @if($lastGroup !== $menu['title'])
            <li class="sidebar-title">
              @switch($menu['title'])
                @case('My Team')
                  Tim Saya
                  @break
                @case('Absensi')
                  Absensi
                  @break
                @case('Lembur')
                  Lembur
                  @break
                @case('Penggajian')
                  Penggajian
                  @break
                @case('Laporan')
                  Laporan
                  @break
                @default
                  {{ $menu['title'] }}
              @endswitch
            </li>
            @php $lastGroup = $menu['title']; @endphp
          @endif
        @endif

        {{-- Render menu item utama --}}
        <li class="sidebar-item {{ request()->route()?->getName() === $menu['route'] ? 'active' : '' }} @if(count($menu['children'] ?? []) > 0) has-sub @endif">
          @if(count($menu['children'] ?? []) > 0)
            {{-- Menu dengan submenu --}}
            <a href="#" class="sidebar-link">
              <i class="bi bi-{{ $menu['icon'] ?? 'dot' }}" 
                 @switch($menu['icon'])
                   @case('home')
                     class="bi bi-grid-fill"
                   @break
                   @case('users')
                     class="bi bi-people-fill"
                   @break
                   @case('calendar')
                     class="bi bi-calendar-check-fill"
                   @break
                   @case('clock')
                     class="bi bi-clock-history"
                   @break
                   @case('dollar-sign')
                     class="bi bi-cash-coin"
                   @break
                   @case('bar-chart')
                     class="bi bi-bar-chart-fill"
                   @break
                   @case('user')
                     class="bi bi-person-circle"
                   @break
                   @default
                     class="bi bi-dot"
                 @endswitch
              ></i>
              <span>{{ $menu['title'] }}</span>
            </a>

            {{-- Submenu items --}}
            <ul class="submenu">
              @foreach($menu['children'] as $child)
                <li class="submenu-item {{ request()->route()?->getName() === $child['route'] ? 'active' : '' }}">
                  <a href="{{ isset($child['route']) ? route($child['route']) : '#' }}" class="submenu-link">
                    {{ $child['title'] }}
                  </a>
                </li>
              @endforeach
            </ul>
          @else
            {{-- Menu tanpa submenu --}}
            <a href="{{ $menu['route'] ? route($menu['route']) : '#' }}" class="sidebar-link">
              <i class="bi bi-{{ $menu['icon'] ?? 'dot' }}"
                 @switch($menu['icon'])
                   @case('home')
                     class="bi bi-grid-fill"
                   @break
                   @case('users')
                     class="bi bi-people-fill"
                   @break
                   @case('calendar')
                     class="bi bi-calendar-check-fill"
                   @break
                   @case('clock')
                     class="bi bi-clock-history"
                   @break
                   @case('dollar-sign')
                     class="bi bi-cash-coin"
                   @break
                   @case('bar-chart')
                     class="bi bi-bar-chart-fill"
                   @break
                   @case('user')
                     class="bi bi-person-circle"
                   @break
                   @default
                     class="bi bi-dot"
                 @endswitch
              ></i>
              <span>
                {{ $menu['title'] }}
                @if(isset($menu['description']))
                  <small class="d-block text-muted">({{ $menu['description'] }})</small>
                @endif
              </span>
            </a>
          @endif
        </li>
      @endforeach
    @else
      {{-- Fallback jika menuStructure tidak tersedia (untuk backward compatibility) --}}
      <li class="sidebar-item {{ request()->routeIs('officers.dashboard') ? 'active' : '' }}">
        <a href="{{ route('officers.dashboard') }}" class="sidebar-link">
          <i class="bi bi-grid-fill"></i>
          <span>Beranda</span>
        </a>
      </li>

      <li class="sidebar-item {{ request()->routeIs('officers.pegawai.*') ? 'active' : '' }}">
        <a href="{{ route('officers.pegawai.index') }}" class="sidebar-link">
          <i class="bi bi-people-fill"></i>
          <span>Tim Saya</span>
        </a>
      </li>

      <li class="sidebar-item {{ request()->routeIs('officers.absensi.*') ? 'active' : '' }}">
        <a href="{{ route('officers.absensi.index') }}" class="sidebar-link">
          <i class="bi bi-calendar-check-fill"></i>
          <span>Absensi</span>
        </a>
      </li>

      <li class="sidebar-item {{ request()->routeIs('officers.lembur.*') ? 'active' : '' }}">
        <a href="{{ route('officers.lembur.index') }}" class="sidebar-link">
          <i class="bi bi-clock-history"></i>
          <span>Lembur</span>
        </a>
      </li>

      <li class="sidebar-item {{ request()->routeIs('officers.penggajian.*') ? 'active' : '' }}">
        <a href="{{ route('officers.penggajian.index') }}" class="sidebar-link">
          <i class="bi bi-cash-coin"></i>
          <span>Penggajian</span>
        </a>
      </li>
    @endif

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

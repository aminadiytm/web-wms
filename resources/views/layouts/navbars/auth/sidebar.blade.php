
<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 " id="sidenav-main">
  <div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
    <a class="align-items-center d-flex m-0 navbar-brand text-wrap" href="{{ route('dashboard') }}">
        <img src="{{ asset('assets/img/favicon_wms.png')}}" class="navbar-brand-img h-100" alt="...">
        <span class="ms-3 font-weight-bold">WMS Dashboard</span>
    </a>
  </div>
  <hr class="horizontal dark mt-0">
  <div class="collapse navbar-collapse  w-auto" id="sidenav-collapse-main">

<ul class="navbar-nav">

    @foreach($sidebarMenus as $groupName => $menus)

        @if($groupName !== 'Main')
            <li class="nav-item mt-2">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">
                    {{ $groupName }}
                </h6>
            </li>
        @endif

        @foreach($menus as $menu)
            <li class="nav-item pb-2">
                <a
                    class="nav-link custom-nav {{ request()->routeIs($menu->menu_route) ? 'active' : '' }}"
                    href="{{ route($menu->menu_route) }}"
                >
                    <div class="nav-icon">
                        <i class="{{ $menu->menu_icon }}"></i>
                    </div>

                    <span class="nav-link-text">
                        {{ $menu->menu_name }}
                    </span>
                </a>
            </li>
        @endforeach

    @endforeach

</ul>

  </div>
</aside>

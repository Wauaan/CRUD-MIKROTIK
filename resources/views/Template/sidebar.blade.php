<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('') }}">
        <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-laugh-wink"></i></div>
        <div class="sidebar-brand-text mx-3">Fatih Group</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item -->
    <li class="nav-item {{ Request::is('/') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>    
    <li class="nav-item {{ Request::is('mikrotik/resources') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('mikrotik.resorces') }}">
            <i class="fas fa-fw fa-brands fa-cogs"></i>
            <span>Resources</span>
        </a>
    </li>
    <li class="nav-item {{ Request::is('mikrotik/interfaces') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('mikrotik.interfaces') }}">
            <i class="fas fa-fw fa-chart-bar"></i>
            <span>Interfaces</span>
        </a>
    </li>
        <li class="nav-item {{ Request::is('nextdns/denylist') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('denylist') }}">
            <i class="fas fa-fw fa-globe"></i>
            <span>Blokir Situs</span>
        </a>
    </li>
    <li class="nav-item {{ Request::is('mikrotik/PPPoE*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePPPoE"
            aria-expanded="false" aria-controls="collapsePPPoE">
            <i class="fas fa-network-wired"></i>
            <span>PPPoE</span>
        </a>
        <div id="collapsePPPoE" class="collapse {{ Request::is('mikrotik/PPPoE*') ? 'show' : '' }}"
            aria-labelledby="headingPPPoE" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('PPPoE.Server') }}">PPPoE Server</a>
                <a class="collapse-item" href="{{ route('PPPoE.Secret') }}">PPPoE Secret</a>
                <a class="collapse-item" href="{{ route('PPPoE.Profile') }}">PPPoE Profile</a>
            </div>
        </div>
    </li>    
        <li class="nav-item {{ Request::is('mikrotik/Hotspot*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseHotspot"
            aria-expanded="false" aria-controls="collapseHotspot">
            <i class="fas fa-rss-square"></i>
            <span>Hotspot</span>
        </a>
        <div id="collapseHotspot" class="collapse {{ Request::is('mikrotik/Hotspot*') ? 'show' : '' }}"
            aria-labelledby="headingHotspot" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('active.hotspot') }}">Hotspot Active User</a>
                <a class="collapse-item" href="{{ route('hotspot.Server.profile') }}">Hotspot Server Profile</a>
                <a class="collapse-item" href="{{ route('hotspot.user') }}">Hotspot Users</a>
                <a class="collapse-item" href="{{ route('hotspot.user.Profile') }}">Hotspot User Profile</a>
            </div>
        </div>
    </li> 
    <li class="nav-item">
    <a class="nav-link" href="{{ route('users.index') }}">
        <i class="fas fa-user-plus"></i>
        <span>Tambah User</span>
    </a>
</li>

</ul>

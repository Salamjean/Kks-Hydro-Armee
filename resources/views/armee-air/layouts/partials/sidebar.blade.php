<nav class="sidebar sidebar-offcanvas dynamic-active-class-disabled" id="sidebar">
    <button class="sidebar-toggle" onclick="toggleSidebar()">
        ☰
    </button>

    <div class="sidebar-menu">
        <div class="logo">
            @php
                $dashboardRouteName = 'corps.gendarmerie.dashboard';
                $logoImagePath = asset('images/default_logo.png');
                $logoAltText = 'Logo par défaut';
                $logoLink = '#';

                $authUser = Auth::guard('corps')->user();

                if ($authUser) {
                    $userCorpsName = $authUser->name;

                    switch ($userCorpsName) {
                        case 'Gendarmerie':
                            $dashboardRouteName = 'corps.gendarmerie.dashboard';
                            $logoImagePath = asset('images/logo_gendarmerie.png');
                            $logoAltText = 'Logo Gendarmerie';
                            break;
                        case 'Marine':
                            $dashboardRouteName = 'corps.marine.dashboard';
                            $logoImagePath = asset('images/logo_marine.jpeg');
                            $logoAltText = 'Logo Marine';
                            break;
                        case 'Armée-Air':
                        case 'Armée Air':
                            $dashboardRouteName = 'corps.armee-air.dashboard';
                            $logoImagePath = asset('images/logo_armee_air.png');
                            $logoAltText = 'Logo Armée de l\'Air';
                            break;
                        case 'Armée-Terre':
                        case 'Armée Terre':
                            $dashboardRouteName = 'corps.armee-terre.dashboard';
                            $logoImagePath = asset('images/logo_armee_terre.jpeg');
                            $logoAltText = 'Logo Armée de Terre';
                            break;
                    }

                    if (Route::has($dashboardRouteName)) {
                        $logoLink = route($dashboardRouteName);
                    }
                }
            @endphp

            <a href="{{ $logoLink }}">
                <img src="{{ $logoImagePath }}" alt="{{ $logoAltText }}">
            </a>
        </div>

        <ul class="menu">
            <li class="sidebar-title">Menu Principal</li>

            <li class="sidebar-item {{ request()->routeIs($dashboardRouteName) ? 'active' : '' }}">
                <a href="{{ route($dashboardRouteName) }}" class="sidebar-link">
                    <i class="bi bi-grid-fill"></i>
                    <span>Tableau de Bord</span>
                </a>
            </li>

            <li class="sidebar-title">Gestion Infrastructure</li>

            <li class="sidebar-item has-sub {{ request()->routeIs('corps.soutes.*') ? 'active' : '' }}">
                <a href="#" class='sidebar-link'>
                    <i class="bi bi-hdd-stack-fill"></i>
                    <span>Soutes</span>
                </a>
                <ul class="submenu {{ request()->routeIs('corps.soutes.*') ? 'active' : '' }}">
                    <li class="submenu-item {{ request()->routeIs('corps.soutes.index') ? 'active' : '' }}">
                        <a href="{{ route('corps.soutes.index') }}">Ajouter/Liste Soutes</a>
                    </li>
                </ul>
            </li>

           <li class="sidebar-item has-sub {{ request()->routeIs('corps.personnel.*') ? 'active' : '' }}">
            <a href="#" class='sidebar-link'>
                <i class="bi bi-people-fill"></i>
                <span>Logistique</span>
            </a>
            <ul class="submenu">
                <li class="submenu-item {{ request()->routeIs('corps.personnel.index') || request()->routeIs('corps.personnel.create') ? 'active' : '' }}">
                    <a href="{{ route('corps.personnel.index') }}">Ajouter un personnel</a>
                </li>
            
            </ul>
        </li>

            <li class="sidebar-item">
                <a href="#" class="sidebar-link">
                    <i class="bi bi-file-earmark-text-fill"></i>
                    <span>Rapports</span>
                </a>
            </li>

            <li class="sidebar-title">Compte</li>

            <li class="sidebar-item">
                <a href="#" class="sidebar-link">
                    <i class="bi bi-person-fill"></i>
                    <span>Mon Profil</span>
                </a>
            </li>

            <li class="sidebar-item">
                <form method="POST" action="{{ route('corps.logout') }}" id="logout-form" style="display: none;">
                    @csrf
                </form>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="sidebar-link">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Déconnexion</span>
                </a>
            </li>
        </ul>
    </div>
</nav>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('active');
    }
    document.addEventListener('DOMContentLoaded', function () {
        const menuItems = document.querySelectorAll('.sidebar-item.has-sub > a');
        menuItems.forEach(function (item) {
            item.addEventListener('click', function (e) {
                e.preventDefault();
                const parent = this.parentElement;
                parent.classList.toggle('active');
                document.querySelectorAll('.sidebar-item.has-sub').forEach(function (other) {
                    if (other !== parent) {
                        other.classList.remove('active');
                    }
                });
            });
        });
    });

</script>
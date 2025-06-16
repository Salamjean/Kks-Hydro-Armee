<nav class="sidebar sidebar-offcanvas dynamic-active-class-disabled" id="sidebar">
    <button class="sidebar-toggle" onclick="toggleSidebar()">
        ☰
    </button>

    <div class="sidebar-menu">
        <div class="logo">

            <a href="{{ route('soute.dashboard.index') }}" class="sidebar-brand">
                <img src="{{ asset('images/logo_soute.png') }}" alt="">
            </a>
        </div>

        <ul class="menu">
            <li class="sidebar-title">Menu Principal</li>

            <li class="sidebar-item ">
                <a href="" class="sidebar-link">
                    <i class="bi bi-grid-fill"></i>
                    <span>Tableau de Bord</span>
                </a>
            </li>
            <li class="sidebar-title">Gestion des Services</li>
            <li class="sidebar-item has-sub {{ request()->routeIs('corps.soutes.*') ? 'active' : '' }}">
                <a href="#" class='sidebar-link'>
                    <i class="bi bi-hdd-stack-fill"></i>
                    <span>Services</span>
                </a>
                <ul class="submenu {{ request()->routeIs('corps.soutes.*') ? 'active' : '' }}">
                    <li class="submenu-item {{ request()->routeIs('corps.soutes.index') ? 'active' : '' }}">
                        <a href="{{ route('soute.dashboard.services.distribution') }}">distribution</a>
                    </li>
                    <li class="submenu-item {{ request()->routeIs('corps.soutes.index') ? 'active' : '' }}">
                        <a href="{{ route('soute.dashboard.services.depotage') }}">dépotage</a>
                    </li>
                </ul>
            </li>

            <li class="sidebar-item">
                <a href="{{ route('soute.dashboard.rapport') }}" class="sidebar-link">
                    <i class="bi bi-file-earmark-text-fill"></i>
                    <span>Rapports</span>
                </a>
            </li>

            <li class="sidebar-item has-sub {{ request()->routeIs('corps.soutes.*') ? 'active' : '' }}">
                <a href="#" class='sidebar-link'>
                    <i class="bi bi-people-fill"></i>
                    <span>Compte</span>
                </a>
                <ul class="submenu">
                    <li class="submenu-item {{ request()->routeIs('soute.dashboard.profile') || request()->routeIs('soute.dashboard.profile') ? 'active' : '' }}">
                        <a href="{{ route('soute.dashboard.profile') }}">Mon Profil</a>
                    </li>
                </ul>
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
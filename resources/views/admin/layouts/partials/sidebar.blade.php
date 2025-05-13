<nav class="sidebar sidebar-offcanvas dynamic-active-class-disabled" id="sidebar">
    <button class="sidebar-toggle" onclick="toggleSidebar()">
        ☰
    </button>

    <div class="sidebar-menu">
        <div class="logo">
            <a href="{{ route('admin.dashboard') }}">
                <img src="" alt="logo admin" class="logo-icon">
            </a>
        </div>

        <ul class="menu">
            <li class="sidebar-title">Menu Principal</li>

            <li class="sidebar-item">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
                    <i class="bi bi-grid-fill"></i>
                    <span>Tableau de Bord</span>
                </a>
            </li>

            <li class="sidebar-title">Gestion des Corps d'armée</li>

            <li class="sidebar-item has-sub {{ request()->routeIs('admin.create.*') ? 'active' : '' }}">
                <a href="#" class='sidebar-link'>
                    <i class="bi bi-hdd-stack-fill"></i>
                    <span>Corps d'armée</span>
                </a>
                <ul class="submenu">
                    <li class="submenu-item {{ request()->routeIs('admin.create.army') ? 'active' : '' }}">
                        <a href="{{ route('admin.create.army') }}">Ajouter</a>
                    </li>
                    <li class="submenu-item ">
                        <a href="{{ route('admin.army') }}">Listes des corps</a>
                    </li>
                </ul>
            </li>
           

            <li class="sidebar-title">Compte</li>

            <li class="sidebar-item">
                <a href="#" class="sidebar-link">
                    <i class="bi bi-person-fill"></i>
                    <span>Mon Profil</span>
                </a>
            </li>

            <li class="sidebar-item">
                <form method="POST" action="{{ route('admin.logout') }}" id="logout-form" style="display: none;">
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
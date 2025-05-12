<nav class="sidebar sidebar-offcanvas dynamic-active-class-disabled" id="sidebar">
  <button class="sidebar-toggle" onclick="toggleSidebar()">
      ☰
  </button>

  <ul class="menu">
    <li class="sidebar-title">Menu Principal</li>
    <li class="sidebar-item {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
        <a href="{{ route('superadmin.dashboard') }}" class="sidebar-link">
            <i class="bi bi-grid-fill"></i>
            <span>Tableau de Bord</span>
        </a>
    </li>

    <li class="sidebar-title">Gestion des SEA</li>
    <li class="sidebar-item has-sub {{ request()->routeIs('superadmin.sea.*') || request()->routeIs('superadmin.create.SEA') ? 'active' : '' }}">
        <a href="#" class='sidebar-link'>
            <i class="bi bi-hdd-stack-fill"></i>
            <span>SEA</span>
        </a>
        <ul class="submenu {{ request()->routeIs('superadmin.sea.*') || request()->routeIs('superadmin.create.SEA') ? 'active' : '' }}">
            <li class="submenu-item {{ request()->routeIs('superadmin.create.SEA') ? 'active' : '' }}">
                <a href="{{ route('superadmin.create.SEA') }}">Ajouter</a>
            </li>
        </ul>
    </li>
    <li class="sidebar-title">Compte</li>

    <li class="sidebar-item {{ request()->routeIs('superadmin.profile') ? 'active' : '' }}">
        <a href="" class="sidebar-link">
            <i class="bi bi-person-fill"></i>
            <span>Mon Profil</span>
        </a>
    </li>

    <li class="sidebar-item">
        <form method="POST" action="{{ route('superadmin.logout') }}" id="logout-form" style="display: none;">
            @csrf
        </form>
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="sidebar-link">
            <i class="bi bi-box-arrow-right"></i>
            <span>Déconnexion</span>
        </a>
    </li>
</ul>
</nav>
<script>
  function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      sidebar.classList.toggle('active');
  }

  document.addEventListener('DOMContentLoaded', function () {
      const menuItemsWithSub = document.querySelectorAll('.sidebar-item.has-sub > a');
      menuItemsWithSub.forEach(function (item) {
          item.addEventListener('click', function (e) {
              e.preventDefault();
              const parentLi = this.parentElement;

              parentLi.classList.toggle('active');

              document.querySelectorAll('.sidebar-item.has-sub').forEach(function (otherLi) {
                  if (otherLi !== parentLi && otherLi.classList.contains('active')) {
                      if (parentLi.classList.contains('active')) {
                         otherLi.classList.remove('active');
                      }
                  }
              });

               const submenuUl = parentLi.querySelector('.submenu');
               if (submenuUl) {
                   if (parentLi.classList.contains('active')) {
                       submenuUl.classList.add('active');
                   } else {
                       submenuUl.classList.remove('active');
                   }
               }
          });
      });

      const activeSubmenus = document.querySelectorAll('.submenu.active');
      activeSubmenus.forEach(submenu => {
      });

  });

</script>
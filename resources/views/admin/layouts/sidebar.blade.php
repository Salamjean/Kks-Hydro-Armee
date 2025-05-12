<div id="sidebar" class="active">
  <div class="sidebar-wrapper active">
      <div class="sidebar-header">
          <div class="d-flex justify-content-between">
              <div class="logo">
                  <a href="{{ route('admin.dashboard') }}"><img src="assets/images/logo/logo.png" alt="Logo" srcset=""></a>
              </div>
              <div class="toggler">
                  <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
              </div>
          </div>
      </div>
      <div class="sidebar-menu">
          <ul class="menu">
              <li class="sidebar-title">Menu</li>

              <li class="sidebar-item active ">
                  <a href="{{ route('admin.dashboard') }}" class='sidebar-link'>
                      <i class="bi bi-grid-fill"></i>
                      <span>Tableau de bord</span>
                  </a>
              </li>

              <li class="sidebar-item  has-sub">
                  <a href="#" class='sidebar-link'>
                      <i class="bi bi-stack"></i>
                      <span>Corps d'armée</span>
                  </a>
                  <ul class="submenu ">
                      <li class="submenu-item ">
                          <a href="{{ route('admin.create.army') }}">Ajout d'un corps</a>
                      </li>
                      <li class="submenu-item ">
                          <a href="{{ route('admin.army') }}">Listes des corps</a>
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
      </div>
      <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
  </div>
</div>
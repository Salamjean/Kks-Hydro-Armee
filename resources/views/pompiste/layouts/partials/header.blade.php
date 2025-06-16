<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex flex-row custom-main-navbar">
  <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
    <ul class="navbar-nav navbar-nav-right">
      
      <li class="nav-item dropdown d-none d-lg-inline-block"> 
        <a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
          <img class="img-xs rounded-circle" src="{{ Auth::guard('corps')->user()->profile_photo_url ?? url('assets/images/faces/face8.jpg') }}" alt="Profile image">
          <span class="profile-text">{{ Auth::guard('corps')->user()->name ?? 'Utilisateur' }}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
          <div class="dropdown-header text-center">
            <img class="img-md rounded-circle" src="{{ Auth::guard('corps')->user()->profile_photo_url ?? url('assets/images/faces/face8.jpg') }}" alt="Profile image">
            <p class="mb-1 mt-3 font-weight-semibold">{{ Auth::guard('corps')->user()->name ?? 'Utilisateur' }}</p>
            <p class="font-weight-light text-muted mb-0">{{ Auth::guard('corps')->user()->email ?? 'email@example.com' }}</p>
          </div>
          <a class="dropdown-item" href="{{ route('soute.dashboard.profile') }}"> 
            <i class="mdi mdi-account-outline text-primary"></i> Mon Profil
          </a>
          <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();">
            <i class="mdi mdi-logout text-primary"></i> Déconnexion
          </a>
           <form id="logout-form-header" action="{{ route('soute.dashboard.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
      </li>
    </ul>
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
      <span class="mdi mdi-menu icon-menu"></span>
    </button>
  </div>
</nav>
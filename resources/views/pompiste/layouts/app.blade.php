<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Soute') - {{ config('app.name', 'Laravel') }}</title>

    {{-- Tes liens CSS communs (tu peux utiliser les mêmes que pour corpsArme ou des spécifiques) --}}
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assetsSEA/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assetsSEA/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assetsSEA/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assetsSEA/css/app-dark.css') }}"> {{-- Si tu utilises le thème sombre --}}

    @yield('styles') {{-- Pour les styles spécifiques à une page du dashboard soute --}}

    <style>
        /* Styles spécifiques pour le layout soute si nécessaire */
        /* Par exemple, une couleur de fond différente pour la sidebar soute */
        /* #sidebar.soute-sidebar { background-color: #3a5a40; } */
    </style>
</head>
<body>
    <script src="{{ asset('assetsSEA/js/initTheme.js') }}"></script> {{-- Pour le thème --}}
    <div id="app">

        {{-- Inclusion de la Sidebar spécifique à la Soute --}}
        @include('soute.layouts.partials.sidebar') {{-- Chemin vers la nouvelle sidebar --}}

        <div id="main" class='layout-navbar'>
            <header class='mb-3'>
                {{-- Tu peux avoir un header différent ou similaire à celui de corpsArme --}}
                <nav class="navbar navbar-expand navbar-light navbar-top">
                     <div class="container-fluid">
                         <a href="#" class="burger-btn d-block">
                             <i class="bi bi-justify fs-3"></i>
                         </a>
                         <div class="collapse navbar-collapse" id="navbarSupportedContent">
                             <ul class="navbar-nav ms-auto mb-lg-0">
                                 {{-- Éléments de header spécifiques à la soute --}}
                             </ul>
                             @auth('personnel_soute') {{-- Vérifie si un personnel_soute est connecté --}}
                             <div class="dropdown">
                                 <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                     <div class="user-menu d-flex">
                                         <div class="user-name text-end me-3">
                                             <h6 class="mb-0 text-gray-600">{{ Auth::guard('personnel_soute')->user()->nom_complet ?? 'Personnel' }}</h6>
                                             <p class="mb-0 text-sm text-gray-600">Soute: {{ Auth::guard('personnel_soute')->user()->soute->nom ?? 'N/A' }}</p>
                                         </div>
                                         <div class="user-img d-flex align-items-center">
                                             <div class="avatar avatar-md">
                                                 {{-- TODO: Image de profil du personnel --}}
                                                 <img src="{{ asset('assetsSEA/images/faces/1.jpg') }}">
                                             </div>
                                         </div>
                                     </div>
                                 </a>
                                 <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton" style="min-width: 11rem;">
                                     <li>
                                         <h6 class="dropdown-header">Bonjour, {{ Auth::guard('personnel_soute')->user()->prenom ?? '' }}!</h6>
                                     </li>
                                     <li><a class="dropdown-item" href="#"><i class="icon-mid bi bi-person me-2"></i> Mon Profil</a></li>
                                     <li>
                                         <hr class="dropdown-divider">
                                     </li>
                                     <li>
                                         <form method="POST" action="{{ route('soute.dashboard.logout') }}" id="soute-logout-form-header" style="display: none;">@csrf</form>
                                         <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('soute-logout-form-header').submit();">
                                             <i class="icon-mid bi bi-box-arrow-right me-2"></i> Déconnexion Soute
                                         </a>
                                      </li>
                                 </ul>
                             </div>
                             @endauth
                         </div>
                     </div>
                 </nav>
            </header>

            <div id="main-content">
                @yield('content')
            </div>

            <footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p>{{ date('Y') }} © Gestion Carburant Soute</p>
                    </div>
                    <div class="float-end">
                        {{-- <p>...</p> --}}
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="{{ asset('assetsSEA/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assetsSEA/js/app.js') }}"></script> {{-- JS principal du template --}}
    @yield('scripts')
</body>
</html>
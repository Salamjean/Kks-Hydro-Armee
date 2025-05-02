<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gendarmerie Nationale') - {{ config('app.name', 'Laravel') }}</title>

    {{-- CSS Communs --}}
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assetsSEA/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assetsSEA/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assetsSEA/css/app.css') }}">
    {{-- CSS pour le thème sombre (si votre template l'a) --}}
    <link rel="stylesheet" href="{{ asset('assetsSEA/css/app-dark.css') }}">
    {{-- Favicon (Optionnel mais recommandé) --}}
    {{-- <link rel="shortcut icon" href="{{ asset('assetsSEA/images/favicon.svg') }}" type="image/x-icon"> --}}

    {{-- Styles spécifiques à la page --}}
    @yield('styles')

     {{-- Style minimal pour que la sidebar fonctionne si app.css ne suffit pas --}}
    <style>
        body.theme-dark #sidebar { background: #2d3748; /* Exemple couleur sombre */}
        /* Ajoutez d'autres styles si nécessaire */
    </style>
</head>
<body>
    <script src="{{ asset('assetsSEA/js/initTheme.js') }}"></script> {{-- Important pour le thème sombre --}}
    <div id="app">

        {{-- **Inclusion de la Sidebar** --}}
        @include('corpsArme.layouts.partials.sidebar')

        {{-- **Conteneur principal (à droite de la sidebar)** --}}
        <div id="main" class='layout-navbar'> {{-- 'layout-navbar' est souvent utilisé si vous avez un header fixe --}}
            <header class='mb-3'>
                <nav class="navbar navbar-expand navbar-light navbar-top">
                     <div class="container-fluid">
                         {{-- Bouton pour afficher/cacher la sidebar sur grand écran --}}
                         <a href="#" class="burger-btn d-block">
                             <i class="bi bi-justify fs-3"></i>
                         </a>

                         <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                             data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                             aria-expanded="false" aria-label="Toggle navigation">
                             <span class="navbar-toggler-icon"></span>
                         </button>
                         <div class="collapse navbar-collapse" id="navbarSupportedContent">
                             <ul class="navbar-nav ms-auto mb-lg-0">
                                 {{-- Ajoutez ici des éléments de header si nécessaire (Notifications, etc.) --}}
                             </ul>
                             <div class="dropdown">
                                 <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                     <div class="user-menu d-flex">
                                         <div class="user-name text-end me-3">
                                             {{-- Nom de l'utilisateur connecté --}}
                                             <h6 class="mb-0 text-gray-600">{{ Auth::guard('corps')->user()->name ?? 'Utilisateur' }}</h6>
                                             <p class="mb-0 text-sm text-gray-600">{{ Auth::guard('corps')->user()->email ?? '' }}</p>
                                         </div>
                                         <div class="user-img d-flex align-items-center">
                                             <div class="avatar avatar-md">
                                                 {{-- Image de profil (optionnel) --}}
                                                 <img src="{{ asset('assetsSEA/images/faces/1.jpg') }}"> {{-- TODO: Mettre image dynamique --}}
                                             </div>
                                         </div>
                                     </div>
                                 </a>
                                 <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton" style="min-width: 11rem;">
                                     <li>
                                         <h6 class="dropdown-header">Bonjour, {{ Str::before(Auth::guard('corps')->user()->name ?? '', ' ') }}!</h6>
                                     </li>
                                     <li><a class="dropdown-item" href="#"><i class="icon-mid bi bi-person me-2"></i> Mon Profil</a></li>
                                     <li><a class="dropdown-item" href="#"><i class="icon-mid bi bi-gear me-2"></i> Paramètres</a></li>
                                     <li>
                                         <hr class="dropdown-divider">
                                     </li>
                                     <li>
                                        {{-- Lien de déconnexion dans le dropdown aussi --}}
                                         <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                             <i class="icon-mid bi bi-box-arrow-right me-2"></i> Déconnexion
                                         </a>
                                      </li>
                                 </ul>
                             </div>
                         </div>
                     </div>
                 </nav>
            </header>

             {{-- **Zone où le contenu spécifique de la page sera injecté** --}}
            <div id="main-content">
                @yield('content')
            </div>

            {{-- **Footer (Optionnel)** --}}
            <footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p>{{ date('Y') }} © Gendarmerie Nationale CI</p>
                    </div>
                    <div class="float-end">
                        <p>Développé avec par <a href="#">KKS-Technologie</a></p>
                    </div>
                </div>
            </footer>
        </div> {{-- Fin de #main --}}
    </div> {{-- Fin de #app --}}

    {{-- JS Communs --}}
    <script src="{{ asset('assetsSEA/js/bootstrap.bundle.min.js') }}"></script>
    {{-- JS principal de votre template (gère souvent la sidebar, etc.) --}}
    <script src="{{ asset('assetsSEA/js/app.js') }}"></script>

    {{-- Scripts spécifiques à la page --}}
    @yield('scripts')
</body>
</html>
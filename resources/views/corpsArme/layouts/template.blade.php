<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title', "Gestion")</title>

    <link rel="shortcut icon" href="{{ asset('/favicon.ico') }}">
    {{-- Assurez-vous que sidebar.css et navbar.css ne sont pas en conflit avec app.css pour les layouts --}}
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/@mdi/font/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    @stack('plugin-styles')
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> {{-- Votre CSS principal Bootstrap & thème --}}
    @stack('styles')

    {{-- Styles CSS pour le layout principal et le footer (idéalement dans un fichier CSS) --}}
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        .container-scroller {
            display: flex;
            flex-direction: column; /* Pour que le header soit au-dessus de page-body-wrapper */
            flex-grow: 1; /* Permet au container-scroller de prendre la hauteur restante */
        }

        /* La hauteur de votre header doit être connue */
        /* Supposons que votre header a une hauteur fixe, par exemple 60px */
        /* Vous devrez peut-être ajuster HauteurDeVotreHeader dynamiquement si elle varie */
        .page-body-wrapper {
            display: flex;
            flex-grow: 1; /* Important pour que cette zone prenne la hauteur restante */
            /* padding-top: HauteurDeVotreHeader;  -- Géré par le positionnement du header si fixe ou sticky */
            width: 100%;
        }

        .main-panel {
            display: flex; /* Ajouté */
            flex-direction: column; /* Ajouté */
            flex-grow: 1;
            margin-left: 260px; /* Largeur de votre sidebar */
            width: calc(100% - 260px);
            transition: width 0.3s ease-in-out, margin-left 0.3s ease-in-out;
            background-color: #f4f4f4; /* Fond du main-panel si content-wrapper ne le couvre pas toujours */
        }

        .content-wrapper {
            padding: 20px;
            width: 100%;
            flex-grow: 1; /* Important pour que le contenu pousse le footer vers le bas */
        }

        /* Pour sidebar repliée */
        body.sidebar-mini .main-panel { /* Assurez-vous que la classe 'sidebar-mini' est ajoutée au body par JS */
            margin-left: 80px; /* Largeur de la sidebar repliée */
            width: calc(100% - 80px);
        }

        /* Responsive pour mobile */
        @media (max-width: 991.98px) {
            .main-panel {
                margin-left: 0;
                width: 100%;
            }
            .sidebar { /* Exemple pour cacher la sidebar et la faire apparaître avec JS */
                transform: translateX(-100%);
                position: fixed; /* ou absolute, selon votre design */
                z-index: 1030; /* Au-dessus du contenu */
                height: 100%;
                /* Ajoutez ici les styles de votre .sidebar (largeur, fond, etc.) */
            }
            .sidebar.open { /* Classe à ajouter avec JS */
                transform: translateX(0);
            }
        }

        /* Styles du Footer */
        .footer-custom {
            background-color: #2c3e50; /* Couleur de fond plus en accord avec un thème "Armée" */
            color: #ecf0f1; /* Texte plus clair */
            padding: 15px 20px; /* Un peu plus de padding */
            text-align: center;
            border-top: 1px solid #34495e; /* Bordure subtile */
            width: 100%; /* Prend toute la largeur */
            /* margin-top: auto; -- Ceci est la clé pour le sticky footer dans un conteneur flex parent en column */
            /* MAIS on va le placer DANS le main-panel pour la simplicité avec la sidebar */
        }

        .footer-link {
            color: #bdc3c7; /* Liens un peu plus discrets */
            text-decoration: none;
            font-weight: 500;
            margin: 0 10px;
        }

        .footer-link:hover {
            text-decoration: underline;
            color: #ffffff; /* Liens plus brillants au survol */
        }

        .footer-custom i {
            vertical-align: middle;
            margin-right: 5px;
        }
    </style>
</head>
<body data-base-url="{{url('/')}}" class="sidebar-lg-only"> {{-- La classe sidebar-lg-only doit être gérée par votre JS --}}

    <div class="container-scroller" id="app">

        @include('corpsArme.layouts.partials.header')

        <div class="container-fluid page-body-wrapper">

            @include('corpsArme.layouts.partials.sidebar')

            <div class="main-panel">
                <div class="content-wrapper">
                    @yield('content')
                </div>
                {{-- PLACEZ LE FOOTER ICI, à l'intérieur du main-panel mais après le content-wrapper --}}
                @include('corpsArme.layouts.partials.footer')
            </div>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    @stack('plugin-scripts')
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('assets/js/misc.js') }}"></script>
    <script src="{{ asset('assets/js/settings.js') }}"></script>
    <script src="{{ asset('assets/js/todolist.js') }}"></script>
    <script src="{{ asset('js/custom-sidebar.js') }}"></script>
    @stack('custom-scripts')
</body>
</html>
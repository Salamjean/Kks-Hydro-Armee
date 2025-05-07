<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative">
            <div class="d-flex justify-content-between align-items-center">
                <div class="logo">
                    {{-- **TODO: Remplacez par le vrai logo ou nom** --}}
                    <a href="{{ route('corps.dashboard') }}"><img src="{{ asset('path/to/gendarmerie_logo.png') }}" alt="Logo" srcset="" style="height: 2rem;"> Gendarmerie</a>
                    {{-- Ou juste du texte : --}}
                    {{-- <a href="{{ route('corps.dashboard') }}" style="font-size: 1.2rem; color: #435ebe;">Gendarmerie CI</a> --}}
                </div>
                {{-- Thème clair/sombre (optionnel, si votre template le gère) --}}
                <div class="theme-toggle d-flex gap-2  align-items-center mt-2">
                     {{-- SVG pour le soleil (thème clair) --}}
                     <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                         role="img" class="iconify iconify--system-uicons" width="20" height="20"
                         preserveAspectRatio="xMidYMid meet" viewBox="0 0 21 21">
                         <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round">
                             <path
                                 d="M10.5 14.5c2.219 0 4-1.763 4-3.982a4.003 4.003 0 0 0-4-4.018c-2.219 0-4 1.781-4 4a4.003 4.003 0 0 0 4 4.018zM10.5 1.5v2m0 14v2M4.5 10.5h-2m14 0h-2M6.343 14.657l-1.414 1.414m11.314-11.314l-1.414 1.414m-9.9 9.9l1.414-1.414M14.657 6.343l1.414-1.414" />
                         </g>
                     </svg>
                     <div class="form-check form-switch fs-6">
                         <input class="form-check-input  me-0" type="checkbox" id="toggle-dark" style="cursor: pointer">
                         <label class="form-check-label"></label>
                     </div>
                     {{-- SVG pour la lune (thème sombre) --}}
                     <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                         role="img" class="iconify iconify--mdi" width="20" height="20" preserveAspectRatio="xMidYMid meet"
                         viewBox="0 0 24 24">
                         <path fill="currentColor"
                            d="m17.75 4.09l-2.53 1.94l.91 3.06l-2.63-1.81l-2.63 1.81l.91-3.06l-2.53-1.94L12.44 4l1.06-3l1.06 3l3.19.09m3.5 6.91l-1.64 1.25l.59 1.98l-1.7-1.17l-1.7 1.17l.59-1.98L15.75 11l2.06-.05L18.5 9l.69 1.95l2.06.05m-2.28 4.95c.83-.08 1.72 1.1 1.19 1.85c-.32.45-.66.87-1.08 1.27C15.17 23 8.84 23 4.94 19.07c-3.91-3.9-3.91-10.24 0-14.14c.4-.4.82-.76 1.27-1.08c.75-.53 1.93.36 1.85 1.19c-.27 2.86.69 5.83 2.89 8.02a9.96 9.96 0 0 0 8.02 2.89m-1.64 2.02a12.08 12.08 0 0 1-7.8-3.47c-2.17-2.19-3.33-5-3.49-7.82c-2.81 2.11-3.5 5.82-2.69 9.02c.9 3.46 3.67 6.22 7.13 7.12c3.18.81 6.89-.09 9.02-2.69c-1.5 1.02-3.21 1.48-5.03 1.48c-.49 0-.99-.02-1.48-.05Z" />
                     </svg>
                </div>
                {{-- Bouton pour cacher/afficher la sidebar sur mobile/tablette --}}
                <div class="sidebar-toggler x">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Menu Principal</li>

                <li class="sidebar-item {{ request()->routeIs('corps.dashboard') || request()->routeIs('corps.gendarmerie.dashboard') ? 'active' : '' }} ">
                    <a href="{{ route('corps.gendarmerie.dashboard') }}" class='sidebar-link'> {{-- Ajuster la route si dynamique --}}
                        <i class="bi bi-grid-fill"></i>
                        <span>Tableau de Bord</span>
                    </a>
                </li>

                {{-- Nouvelle Section Gestion Carburant --}}
                <li class="sidebar-title">Gestion Carburant</li>
                 <li class="sidebar-item has-sub {{ request()->routeIs('corps.carburant.*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-fuel-pump-fill"></i>
                        <span>Carburant</span>
                    </a>
                    <ul class="submenu {{ request()->routeIs('corps.carburant.*') ? 'active' : '' }}">
                        <li class="submenu-item {{ request()->routeIs('corps.carburant.create') ? 'active' : '' }}">
                            <a href="#">Nouvelle Transaction</a> {{-- -> route('corps.carburant.create') --}}
                        </li>
                        <li class="submenu-item {{ request()->routeIs('corps.carburant.index') ? 'active' : '' }}">
                            <a href="#">Historique Transactions</a> {{-- -> route('corps.carburant.index') --}}
                        </li>
                         <li class="submenu-item ">
                            <a href="#">Suivi Inventaire</a> {{-- -> route('corps.carburant.inventaire') --}}
                        </li>
                    </ul>
                </li>


                {{-- Nouvelle Section Gestion Personnel --}}
                <li class="sidebar-title">Gestion Ressources</li>
                 <li class="sidebar-item has-sub {{ request()->routeIs('corps.personnel.*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-people-fill"></i>
                        <span>Personnel</span>
                    </a>
                    <ul class="submenu {{ request()->routeIs('corps.personnel.*') ? 'active' : '' }}">
                        <li class="submenu-item {{ request()->routeIs('corps.personnel.create') ? 'active' : '' }}">
                            <a href="#">Ajouter Employé</a> {{-- -> route('corps.personnel.create') --}}
                        </li>
                        <li class="submenu-item {{ request()->routeIs('corps.personnel.index') ? 'active' : '' }}">
                            <a href="#">Liste Employés</a> {{-- -> route('corps.personnel.index') --}}
                        </li>
                    </ul>
                </li>

                 {{-- Nouvelle Section Gestion Services --}}
                 <li class="sidebar-item has-sub {{ request()->routeIs('corps.services.*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-building"></i>
                        <span>Services</span>
                    </a>
                    <ul class="submenu {{ request()->routeIs('corps.services.*') ? 'active' : '' }}">
                         <li class="submenu-item {{ request()->routeIs('corps.services.create') ? 'active' : '' }}">
                            <a href="#">Ajouter Service</a> {{-- -> route('corps.services.create') --}}
                        </li>
                        <li class="submenu-item {{ request()->routeIs('corps.services.index') ? 'active' : '' }}">
     <a href="{{ route('corps.services.index') }}">Liste des Services</a> {{-- Vérifie cette ligne --}}
 </li>
                    </ul>
                </li>

                {{-- Nouvelle Section Gestion Distributeurs --}}
                <li class="sidebar-item has-sub {{ request()->routeIs('corps.distributeurs.*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-truck"></i> {{-- Ou une icône de pompe --}}
                        <span>Distributeurs</span>
                    </a>
                    <ul class="submenu {{ request()->routeIs('corps.distributeurs.*') ? 'active' : '' }}">
                         <li class="submenu-item {{ request()->routeIs('corps.distributeurs.create') ? 'active' : '' }}">
                            <a href="#">Ajouter Distributeur</a> {{-- -> route('corps.distributeurs.create') --}}
                        </li>
                        <li class="submenu-item {{ request()->routeIs('corps.distributeurs.index') ? 'active' : '' }}">
                            <a href="#">Liste Distributeurs</a> {{-- -> route('corps.distributeurs.index') --}}
                        </li>
                        <li class="submenu-item ">
                            <a href="#">Maintenance</a> {{-- -> route('corps.distributeurs.maintenance') --}}
                        </li>
                    </ul>
                </li>

                 <li class="sidebar-item">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-file-earmark-text-fill"></i>
                        <span>Rapports</span>
                    </a>
                </li>

                <li class="sidebar-title">Compte</li>

                 <li class="sidebar-item">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-person-fill"></i>
                        <span>Mon Profil</span>
                    </a>
                </li>

                <li class="sidebar-item">
                     {{-- Utilisation d'un formulaire pour la déconnexion (plus sécurisé) --}}
                     <form method="POST" action="{{ route('corps.logout') }}" id="logout-form" style="display: none;">
                        @csrf
                    </form>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class='sidebar-link'>
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Déconnexion</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>
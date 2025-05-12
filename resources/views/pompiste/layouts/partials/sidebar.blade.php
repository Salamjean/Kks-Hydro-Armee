<div id="sidebar" class="active soute-sidebar"> {{-- Ajout d'une classe 'soute-sidebar' pour personnalisation CSS si besoin --}}
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative">
            <div class="d-flex justify-content-between align-items-center">
                <div class="logo">
                    @auth('personnel_soute')
                        <a href="{{ route('soute.dashboard.index') }}">
                            {{-- Tu peux mettre une icône ou le nom de la soute --}}
                            <i class="bi bi-hdd-stack-fill fs-4 me-2"></i>
                            <span>Soute: {{ Str::limit(Auth::guard('personnel_soute')->user()->soute->nom ?? 'Dashboard', 15) }}</span>
                        </a>
                    @endauth
                </div>
                {{-- Thème clair/sombre --}}
                <div class="theme-toggle d-flex gap-2  align-items-center mt-2">
                    {{-- ... (code des icônes de thème identique à l'autre sidebar) ... --}}
                </div>
                <div class="sidebar-toggler x">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Menu Soute</li>

                <li class="sidebar-item {{ request()->routeIs('soute.dashboard.index') ? 'active' : '' }} ">
                    <a href="{{ route('soute.dashboard.index') }}" class='sidebar-link'>
                        <i class="bi bi-grid-fill"></i>
                        <span>Vue d'Ensemble Soute</span>
                    </a>
                </li>

                <li class="sidebar-title">Opérations</li>

                {{-- Lien vers la page des transactions (qui contient la modale) --}}
                <li class="sidebar-item {{ request()->routeIs('corps.carburants.index') ? 'active' : '' }}">
                    <a href="{{ route('corps.carburants.index') }}" class='sidebar-link'>
                        <i class="bi bi-fuel-pump"></i>
                        <span>Enregistrer Sortie</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="#" class='sidebar-link'> {{-- TODO: Route pour enregistrer une entrée de carburant --}}
                        <i class="bi bi-truck"></i>
                        <span>Enregistrer Entrée</span>
                    </a>
                </li>

                <li class="sidebar-item has-sub {{-- request()->routeIs('soute.distributeurs.*') --}}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-diagram-3-fill"></i>
                        <span>Distributeurs (Pompes)</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item ">
                             {{-- Normalement, la gestion (ajout/modif) des pompes se fait par le corps d'armée, pas le personnel de soute --}}
                            <a href="{{ route('corps.distributeurs.index') }}">Voir les Pompes</a>
                        </li>
                        <li class="submenu-item ">
                            <a href="#">Niveaux Pompes</a> {{-- TODO: Vue spécifique --}}
                        </li>
                    </ul>
                </li>


                <li class="sidebar-title">Consultation</li>
                <li class="sidebar-item ">
                    <a href="{{ route('corps.carburants.index') }}" class='sidebar-link'>
                        <i class="bi bi-list-task"></i>
                        <span>Historique Transactions</span>
                    </a>
                </li>
                <li class="sidebar-item ">
                    <a href="#" class='sidebar-link'> {{-- TODO: Route pour le stock --}}
                        <i class="bi bi-archive-fill"></i>
                        <span>État du Stock</span>
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
                    <form method="POST" action="{{ route('soute.dashboard.logout') }}" id="soute-logout-form-sidebar" style="display: none;">
                        @csrf
                    </form>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('soute-logout-form-sidebar').submit();" class='sidebar-link'>
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Déconnexion</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
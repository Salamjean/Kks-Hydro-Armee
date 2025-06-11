@extends('pompiste.layouts.template')

@section('title', 'Tableau de Bord - Soute ' . ($soute->nom ?? ''))

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3>Tableau de Bord Soute : {{ $soute->nom ?? 'Inconnue' }}</h3>
                <p class="text-subtitle text-muted">Matricule Soute : <strong>{{ $soute->matricule_soute ?? 'N/A' }}</strong></p>
            </div>
            <div>
                <p class="text-end mb-0">Pompiste : {{ $personnel->nom_complet ?? 'Employé inconnu' }}</p>
                <p class="text-end text-muted mb-0">Matricule : {{ $personnel->matricule ?? '' }}</p>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <div class="row">
                <div class="col-12">
                    <h4 class="mb-3">État des Stocks Carburant</h4>
                </div>
            </div>
            <div class="row">
                @php
                $fuelsData = []; // Renommé pour clarté
                // Générer dynamiquement les données carburants basées sur la soute
                // et déterminer le niveau actuel pour l'affichage
                if (isset($soute) && is_array($soute->types_carburants_stockes)) {
                    if (in_array('Diesel', $soute->types_carburants_stockes)) {
                        $niveauActuelDiesel = $soute->niveau_actuel_diesel !== null ? $soute->niveau_actuel_diesel : $soute->capacite_diesel;
                        $fuelsData[] = (object)[
                            'type' => 'Diesel',
                            'capacite_totale' => (float)($soute->capacite_diesel ?? 0),
                            'niveau_pour_affichage' => (float)($niveauActuelDiesel ?? 0), // Ce qui est réellement disponible ou était là
                            'icon_class' => 'bi bi-truck text-primary'
                        ];
                    }
                    if (in_array('Essence', $soute->types_carburants_stockes)) {
                        $niveauActuelEssence = $soute->niveau_actuel_essence !== null ? $soute->niveau_actuel_essence : $soute->capacite_essence;
                        $fuelsData[] = (object)[
                            'type' => 'Essence',
                            'capacite_totale' => (float)($soute->capacite_essence ?? 0),
                            'niveau_pour_affichage' => (float)($niveauActuelEssence ?? 0),
                            'icon_class' => 'bi bi-car-front-fill text-success'
                        ];
                    }
                    if (in_array('Kerozen', $soute->types_carburants_stockes)) {
                        $niveauActuelKerozen = $soute->niveau_actuel_kerozen !== null ? $soute->niveau_actuel_kerozen : $soute->capacite_kerozen;
                        $fuelsData[] = (object)[
                            'type' => 'Kerozen',
                            'capacite_totale' => (float)($soute->capacite_kerozen ?? 0),
                            'niveau_pour_affichage' => (float)($niveauActuelKerozen ?? 0),
                            'icon_class' => 'bi bi-airplane-engines-fill text-info'
                        ];
                    }
                }
            @endphp

                @if(!empty($fuelsData))
                    @foreach($fuelsData as $fuel)
                        @php
                            $type = $fuel->type;
                            $seuil = $soute->{"seuil_alert_$type"};
                            $capaciteMax = $fuel->capacite_totale; // La capacité totale de la soute pour ce carburant
                            $niveauAffiche = $fuel->niveau_pour_affichage; // Le stock actuel à afficher
                            $estEnAlerte = !is_null($seuil) && $niveauAffiche <= $seuil;
                            // Le pourcentage est basé sur le niveau actuel par rapport à la capacité maximale
                            $pourcentage = ($capaciteMax > 0) ? (($niveauAffiche / $capaciteMax) * 100) : 0;
                            $pourcentage = round(min(max($pourcentage, 0), 100));

                            $fuelLevelColorClass = ''; // Initialiser la classe de couleur

                            if ($niveauAffiche <= 0) { // Stock vide ou négatif (ne devrait pas être négatif avec la logique actuelle)
                                $fuelLevelColorClass = 'bg-secondary'; // GRIS pour vide
                                $pourcentage = 0; // Forcer 0% si le niveau est 0 ou moins
                            } elseif ($pourcentage < 25) {
                                $fuelLevelColorClass = 'bg-danger'; // ROUGE pour < 25%
                            } elseif ($pourcentage < 50) {
                                $fuelLevelColorClass = 'bg-orange'; // ORANGE pour 25% à 49% (vous n'avez pas de classe CSS bg-orange par défaut dans Bootstrap, utilisez bg-warning ou définissez bg-orange)
                                // Si vous n'avez pas bg-orange, utilisons bg-warning pour ce seuil
                                // $fuelLevelColorClass = 'bg-warning';
                            } elseif ($pourcentage < 75) {
                                $fuelLevelColorClass = 'bg-warning'; // JAUNE (warning) pour 50% à 74%
                            } elseif ($pourcentage < 100) {
                                $fuelLevelColorClass = 'bg-success'; // VERT pour 75% à 99%
                            } else { // $pourcentage == 100
                                $fuelLevelColorClass = 'bg-primary'; // BLEU pour 100% (plein)
                            }
                        @endphp

                        <div class="col-md-4 mb-4"> {{-- Ajout de mb-4 pour espacement vertical --}}
                            <div class="card h-100 @if($estEnAlerte) border-danger @endif">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="{{ $fuel->icon_class }}"></i>
                                        {{ $type }}
                                    </h5>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                    <div class="fuel-tank-container mb-3">
                                        <div class="fuel-tank">
                                            <div class="fuel-level {{ $fuelLevelColorClass }}" style="height: {{ $pourcentage }}%;">
                                                {{-- Afficher le pourcentage seulement si > 0 pour ne pas surcharger le gris --}}
                                                @if($pourcentage > 0)
                                                    <span>{{ $pourcentage }}%</span>
                                                @endif
                                            </div>
                                            <div class="graduations">
                                                <div class="graduation-mark" style="bottom: 0%;">0%</div>
                                                <div class="graduation-mark" style="bottom: 25%;">25%</div>
                                                <div class="graduation-mark" style="bottom: 50%;">50%</div>
                                                <div class="graduation-mark" style="bottom: 75%;">75%</div>
                                                <div class="graduation-mark" style="top: 0; transform: translateY(-50%);">100%</div>
                                            </div>
                                        </div>
                                        <div class="tank-info mt-2">
                                            <p class="mb-0">Niveau: 
                                                <strong>{{ number_format($niveauAffiche, 0, ',', ' ') }} L</strong>
                                            </p>
                                            
                                            @if(!is_null($seuil))
                                                <p class="mb-0 @if($estEnAlerte) text-danger @endif">
                                                    Seuil alerte: {{ number_format($seuil, 0, ',', ' ') }} L
                                                </p>
                                            @endif
                                            
                                            <p class="text-muted">Capacité: {{ number_format($capaciteMax, 0, ',', ' ') }} L</p>
                                        </div>
                                    </div>

                                    @if($capaciteMax > 0 && $niveauAffiche > $capaciteMax)
                                        <small class="text-danger d-block mt-1"><i class="bi bi-exclamation-triangle-fill"></i> Niveau actuel dépasse la capacité !</small>
                                    @elseif ($niveauAffiche > 0 && $capaciteMax <= 0) {{-- Cas où capacité non définie mais niveau si --}}
                                        <small class="text-warning d-block mt-1"><i class="bi bi-exclamation-triangle-fill"></i> Capacité totale non définie.</small>
                                    @endif
                                </div>
                                <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Remplissage : {{ $pourcentage }}%</small>
                                    {{-- Vous pourriez ajouter d'autres infos ici si besoin --}}
                                </div>
                                @if($estEnAlerte)
                                <div class="card-footer bg-danger text-white">
                                    <i class="bi bi-exclamation-triangle-fill"></i> 
                                    Alerte seuil atteint ({{ $seuil }} L)
                                </div>
                            @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="alert alert-info text-center" role="alert">
                            @if(!isset($soute))
                                Les informations de la soute ne sont pas disponibles.
                            @elseif(empty($soute->types_carburants_stockes) || !is_array($soute->types_carburants_stockes))
                                Aucun type de carburant n'est configuré pour être stocké dans cette soute.
                            @else
                                Aucune donnée de carburant à afficher pour cette soute.
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <section class="row mt-4"> {{-- Ajout de mt-4 --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Informations Générales de la Soute</h4>
                    </div>
                    <div class="card-body">
                        <p><i class="bi bi-geo-alt-fill"></i> Localisation : {{ $soute->localisation ?? 'N/A' }}</p>
                        @if($soute->description)
                            <p><i class="bi bi-info-circle-fill"></i> Description : {{ $soute->description }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <section class="section mt-4">
            <div class="card">
                <div class="card-header">
                    <h4>Actions Rapides</h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2 d-md-block">
                        <a href="{{ route('soute.dashboard.services.distribution') }}" class="btn btn-primary">
                            <i class="bi bi-fuel-pump"></i> Faire une Distribution
                        </a>
                        {{-- Vous pourriez ajouter un bouton pour "Enregistrer un Dépotage" ici plus tard --}}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@push('scripts') 

@endpush

<style>
    .fuel-tank-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        font-family: Arial, sans-serif;
    }

    .fuel-tank {
        width: 180px; /* Maintenu pour correspondre à l'image */
        height: 300px; /* Maintenu pour correspondre à l'image */
        border: 3px solid #555;
        background-color: #e0e0e0; /* Gris clair pour le fond du tank vide */
        position: relative;
        border-radius: 10px 10px 5px 5px; /* Bords arrondis comme sur l'image */
        margin: 0 auto; /* Centrer le tank */
        overflow: hidden; /* Pour que le fuel-level ne dépasse pas avec border-radius */
    }

    .fuel-level {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        /* La couleur de fond est gérée par $fuelLevelColorClass (bg-success, bg-warning, bg-danger) */
        transition: height 0.5s ease-in-out, background-color 0.5s ease-in-out;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 0.9em; /* Ajusté pour une meilleure lisibilité */
        text-shadow: 1px 1px 2px rgba(0,0,0,0.7);
    }

    .graduations {
        position: absolute;
        top: 0;
        left: -35px; /* Positionner à gauche du tank */
        height: 100%;
        width: 30px; /* Largeur pour les graduations */
        font-size: 0.7em;
        color: #333;
    }

    .graduation-mark {
        position: absolute;
        width: 100%;
        text-align: right; /* Aligner le texte à droite, près du tank */
        padding-right: 5px; /* Petit espace avant le tiret */
    }
    .graduation-mark::after { 
        content: "—"; /* Tiret de graduation */
        position: absolute;
        right: -5px; /* Positionner le tiret pour qu'il touche/dépasse légèrement vers le tank */
        top: 50%;
        transform: translateY(-50%);
    }

    /* Assurer la priorité des couleurs de fond pour le niveau de carburant */
    .fuel-level.bg-success { background-color: #28a745 !important; } /* Vert */
    .fuel-level.bg-warning { background-color: #ffc107 !important; } /* Jaune/Orange */
    .fuel-level.bg-danger  { background-color: #dc3545 !important; } /* Rouge */

    .tank-info {
        text-align: center;
        margin-top: 8px; /* Espace au-dessus des informations */
    }
    .tank-info p {
        margin-bottom: 2px;
        font-size: 0.9em; /* Taille de police pour les infos */
    }
    /* Couleur Orange personnalisée si Bootstrap ne l'a pas par défaut */
    .bg-orange {
        background-color: #fd7e14 !important; /* Couleur orange de Bootstrap (si elle existait) ou une de votre choix */
    }
    .bg-secondary { /* Assurez-vous que Bootstrap a une couleur bg-secondary ou définissez-la */
        background-color: #6c757d !important; /* Gris standard de Bootstrap */
    }
</style>
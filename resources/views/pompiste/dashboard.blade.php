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

    {{-- Alerte générale si un carburant en-dessous du seuil d'alerte (optionnel, on garde l’ancienne logique ou adapte) --}}
    @isset($fuelsData)
        @php
            $alerteActive = false;
            $messagesAlertes = [];

            foreach ($fuelsData as $fuel) {
                $typeRaw = $fuel->type;
                // On suppose que les champs dans $soute sont nommés en minuscules, par ex. seuil_alert_diesel
                $typeLower = strtolower($typeRaw);
                $niveauAffiche = $fuel->niveau_pour_affichage;
                $seuilAlerte = $soute->{"seuil_alert_$typeLower"} ?? null;
                $seuilIndispo = $soute->{"seuil_indisponibilite_$typeLower"} ?? null;

                if (!is_null($seuilIndispo) && $niveauAffiche <= $seuilIndispo) {
                    $alerteActive = true;
                    $messagesAlertes[] = "⚠ Le niveau de <strong>{$typeRaw}</strong> a atteint le seuil d'indisponibilité";
                }
                elseif (!is_null($seuilAlerte) && $niveauAffiche <= $seuilAlerte) {
                    $alerteActive = true;
                    $messagesAlertes[] = "⚠ Le niveau de <strong>{$typeRaw}</strong> est en dessous du seuil d'alerte";
                }
            }
        @endphp

        @if($alerteActive)
            <div class="alert alert-danger shadow-sm mb-4 rounded-3 border border-danger-subtle">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-octagon-fill me-3 fs-3 text-danger"></i>
                    <div>
                        <h5 class="mb-1 fw-bold">Alerte de seuil critique !</h5>
                        @foreach($messagesAlertes as $msg)
                            <p class="mb-0">{!! $msg !!}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endisset

    <div class="chart-container" style="width: 100%; margin: auto;">
        <h4 style="text-align: center;">Distribution et Dépotage (L)</h4>
        <canvas id="combinedChart" style="width: 100%; height: 30vh;"></canvas>
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
                    // Générer dynamiquement les données carburants basées sur la soute
                    $fuelsData = []; // On peut renommer si nécessaire, veille à ne pas écraser une variable précédente
                    if (isset($soute) && is_array($soute->types_carburants_stockes)) {
                        if (in_array('Diesel', $soute->types_carburants_stockes)) {
                            $niveauActuelDiesel = $soute->niveau_actuel_diesel !== null ? $soute->niveau_actuel_diesel : $soute->capacite_diesel;
                            $fuelsData[] = (object)[
                                'type' => 'Diesel',
                                'capacite_totale' => (float)($soute->capacite_diesel ?? 0),
                                'niveau_pour_affichage' => (float)($niveauActuelDiesel ?? 0),
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
                            $typeRaw = $fuel->type; // ex. 'Diesel'
                            $typeLower = strtolower($typeRaw); // ex. 'diesel', pour accéder aux champs : seuil_alert_diesel, etc.
                            $capaciteMax = $fuel->capacite_totale;
                            // Évite division par zéro :
                            if ($capaciteMax <= 0) {
                                $capaciteMax = 1;
                            }
                            $niveauAffiche = $fuel->niveau_pour_affichage;
                            // Pourcentage arrondi 0-100
                            $pourcentage = ($niveauAffiche / $capaciteMax) * 100;
                            $pourcentage = round(min(max($pourcentage, 0), 100));

                            // Récupère les seuils depuis l'objet $soute (ou null si non défini)
                            $seuilAlerte = $soute->{"seuil_alert_$typeLower"} ?? null;
                            $seuilIndisponibilite = $soute->{"seuil_indisponibilite_$typeLower"} ?? null;

                            $estIndisponible = !is_null($seuilIndisponibilite) && $niveauAffiche <= $seuilIndisponibilite;
                            $estEnAlerte = !is_null($seuilAlerte) && $niveauAffiche <= $seuilAlerte && !$estIndisponible;

                            // Détermination de la couleur de la jauge
                            if ($niveauAffiche <= 0) {
                                $fuelLevelColorClass = 'bg-secondary';
                                $pourcentage = 0;
                            }
                            elseif ($estIndisponible) {
                                $fuelLevelColorClass = 'bg-danger';
                            }
                            elseif ($estEnAlerte) {
                                $fuelLevelColorClass = 'bg-warning';
                            }
                            elseif ($pourcentage < 25) {
                                $fuelLevelColorClass = 'bg-danger';
                            }
                            elseif ($pourcentage < 50) {
                                $fuelLevelColorClass = 'bg-orange';
                            }
                            elseif ($pourcentage < 75) {
                                $fuelLevelColorClass = 'bg-warning';
                            }
                            elseif ($pourcentage < 100) {
                                $fuelLevelColorClass = 'bg-success';
                            }
                            else {
                                $fuelLevelColorClass = 'bg-primary';
                            }
                        @endphp

                        <div class="col-md-4 mb-4">
                            <div class="card h-100 
                                @if($estIndisponible) border-danger 
                                @elseif($estEnAlerte) border-warning 
                                @endif
                            ">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="{{ $fuel->icon_class }}"></i>
                                        {{ $typeRaw }}
                                    </h5>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                    <div class="fuel-tank-container mb-3">
                                        <div class="fuel-tank">
                                            <div class="fuel-level {{ $fuelLevelColorClass }}" style="height: {{ $pourcentage }}%;">
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
                                            @if(!is_null($seuilAlerte))
                                                <p class="mb-0 @if($estEnAlerte) text-warning @endif">
                                                    Seuil alerte: {{ number_format($seuilAlerte, 0, ',', ' ') }} L
                                                </p>
                                            @endif
                                            @if(!is_null($seuilIndisponibilite))
                                                <p class="mb-0 @if($estIndisponible) text-danger @endif">
                                                    Seuil indisponibilité: {{ number_format($seuilIndisponibilite, 0, ',', ' ') }} L
                                                </p>
                                            @endif
                                            <p class="text-muted">Capacité: {{ number_format($fuel->capacite_totale, 0, ',', ' ') }} L</p>
                                        </div>
                                    </div>

                                    @if($fuel->capacite_totale > 0 && $niveauAffiche > $fuel->capacite_totale)
                                        <small class="text-danger d-block mt-1">
                                            <i class="bi bi-exclamation-triangle-fill"></i> Niveau actuel dépasse la capacité !
                                        </small>
                                    @elseif ($niveauAffiche > 0 && $fuel->capacite_totale <= 0)
                                        <small class="text-warning d-block mt-1">
                                            <i class="bi bi-exclamation-triangle-fill"></i> Capacité totale non définie.
                                        </small>
                                    @endif
                                </div>
                                <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Remplissage : {{ $pourcentage }}%</small>
                                </div>
                                @if($estIndisponible)
                                    <div class="card-footer bg-danger text-white">
                                        <i class="bi bi-exclamation-triangle-fill"></i> 
                                        Seuil d'indisponibilité atteint
                                    </div>
                                @elseif($estEnAlerte)
                                    <div class="card-footer bg-warning text-dark">
                                        <i class="bi bi-exclamation-triangle-fill"></i> 
                                        Seuil d'alerte atteint
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

        <section class="row mt-4">
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
                        {{-- Tu peux ajouter un bouton pour "Enregistrer un Dépotage" ici --}}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('combinedChart');

        // Données fictives pour les mois et les valeurs (à remplacer par de vraies données si dispo)
        const labels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'];
        const distributionData = [1200, 1500, 1100, 1800, 1600, 1400];
        const depotageData = [1000, 1300, 900, 1700, 1500, 1200];

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Distribution (L)',
                        data: distributionData,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Dépotage (L)',
                        data: depotageData,
                        backgroundColor: 'rgba(255, 159, 64, 0.7)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: "Capacité (L)"
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: "Mois"
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                if (context.parsed.y !== null) label += context.parsed.y + ' L';
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });
</script>

<style>
    .fuel-tank-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        font-family: Arial, sans-serif;
    }

    .fuel-tank {
        width: 180px;
        height: 300px;
        border: 3px solid #555;
        background-color: #e0e0e0;
        position: relative;
        border-radius: 10px 10px 5px 5px;
        margin: 0 auto;
        overflow: hidden;
    }

    .fuel-level {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        transition: height 0.5s ease-in-out, background-color 0.5s ease-in-out;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 0.9em;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.7);
    }

    .graduations {
        position: absolute;
        top: 0;
        left: -35px;
        height: 100%;
        width: 30px;
        font-size: 0.7em;
        color: #333;
    }

    .graduation-mark {
        position: absolute;
        width: 100%;
        text-align: right;
        padding-right: 5px;
    }
    .graduation-mark::after {
        content: "—";
        position: absolute;
        right: -5px;
        top: 50%;
        transform: translateY(-50%);
    }

    /* Couleurs pour le niveau */
    .fuel-level.bg-success { background-color: #28a745 !important; }
    .fuel-level.bg-warning { background-color: #ffc107 !important; }
    .fuel-level.bg-danger  { background-color: #dc3545 !important; }
    .fuel-level.bg-secondary { background-color: #6c757d !important; }

    .tank-info {
        text-align: center;
        margin-top: 8px;
    }
    .tank-info p {
        margin-bottom: 2px;
        font-size: 0.9em;
    }
    .bg-orange {
        background-color: #fd7e14 !important;
    }
</style>

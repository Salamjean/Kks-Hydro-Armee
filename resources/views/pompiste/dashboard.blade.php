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
                    
                    $fictionalFuels = [
                        (object)[
                            'type' => 'Diesel',
                            'capacite' => 10000,
                            'niveau_actuel' => 7500,
                            'icon_class' => 'bi bi-truck text-primary'
                        ],
                        (object)[
                            'type' => 'Essence',
                            'capacite' => 8000,
                            'niveau_actuel' => 1800,
                            'icon_class' => 'bi bi-car-front-fill text-success'
                        ],
                        (object)[
                            'type' => 'Kerozen',
                            'capacite' => 12000,
                            'niveau_actuel' => 5400,
                            'icon_class' => 'bi bi-airplane-engines-fill text-info'
                        ],
                    ];
                @endphp
            
                @if(!empty($fictionalFuels))
                    @foreach($fictionalFuels as $fuel)
                        @php
                            $type = $fuel->type;
                            $capacite = $fuel->capacite;
                            $niveau = $fuel->niveau_actuel;
            
                            $pourcentage = ($capacite > 0) ? ($niveau / $capacite) * 100 : 0;
                            $pourcentage = round(min(max($pourcentage, 0), 100)); // S'assurer que c'est entre 0 et 100
            
                            $fuelLevelColorClass = 'bg-success'; // Vert par défaut
                            if ($pourcentage < 25) {
                                $fuelLevelColorClass = 'bg-danger'; // Rouge si < 25%
                            } elseif ($pourcentage < 50) {
                                $fuelLevelColorClass = 'bg-warning'; // Orange si < 50%
                            }
                        @endphp
            
                        <div class="col-md-4"> {{-- d-flex et align-items-stretch pour cartes de même hauteur --}}
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        {{-- Utilisation de la classe d'icône définie dans les données fictives --}}
                                        <i class="{{ $fuel->icon_class }}"></i>
                                        {{ $type }}
                                    </h5>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-center align-items-center"> {{-- Centrer le contenu --}}
                                    <div class="fuel-tank-container mb-3">
                                        <div class="fuel-tank">
                                            <div class="fuel-level {{ $fuelLevelColorClass }}" style="height: {{ $pourcentage }}%;">
                                                <span>{{ $pourcentage }}%</span>
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
                                            <p class="mb-0">Niveau: <strong>{{ number_format($niveau, 0, ',', ' ') }} L</strong></p>
                                            <p class="text-muted">Capacité: {{ number_format($capacite, 0, ',', ' ') }} L</p>
                                        </div>
                                    </div>
            
                                    @if($capacite > 0 && $niveau > $capacite)
                                        <small class="text-danger d-block mt-1"><i class="bi bi-exclamation-triangle-fill"></i> Niveau actuel dépasse la capacité !</small>
                                    @elseif ($niveau > $capacite && $capacite <= 0) {{-- Cas où capacité non définie mais niveau si --}}
                                        <small class="text-warning d-block mt-1"><i class="bi bi-exclamation-triangle-fill"></i> Capacité non définie.</small>
                                    @endif
                                </div>
                                <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Remplissage : {{ $pourcentage }}%</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="alert alert-info text-center" role="alert">
                            Aucune donnée fictive de carburant à afficher.
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <section class="row">
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
                        <a href="{{ route('corps.carburants.index', ['soute_id' => $soute->id]) }}" class="btn btn-primary">
                            <i class="bi bi-fuel-pump"></i> Enregistrer une Sortie de Carburant
                        </a>
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
        background-color: green;
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

    .fuel-level.bg-success { background-color: #28a745 !important; }
    .fuel-level.bg-warning { background-color: #ffc107 !important; }
    .fuel-level.bg-danger  { background-color: #dc3545 !important; }

    .tank-info {
        text-align: center;
        margin-top: 8px;
    }
    .tank-info p {
        margin-bottom: 2px;
        font-size: 0.9em;
    }
</style>
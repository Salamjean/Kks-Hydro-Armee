@extends('pompiste.layouts.app') {{-- Assure-toi que ce layout existe et est correct --}}

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

    <section class="section">
        <div class="row">
            <div class="col-12">
                <h4 class="mb-3">État des Stocks Carburant</h4>
            </div>
        </div>
        <div class="row">
            {{-- Boucler sur les types de carburant stockés dans la soute --}}
            @if(!empty($soute->types_carburants_stockes) && is_array($soute->types_carburants_stockes))
                @foreach($soute->types_carburants_stockes as $type)
                    @php
                        $capaciteKey = 'capacite_' . strtolower(str_replace('-', '_', $type)); // ex: capacite_diesel
                        $niveauKey = 'niveau_actuel_' . strtolower(str_replace('-', '_', $type)); // ex: niveau_actuel_diesel
                        $capacite = $soute->$capaciteKey ?? 0;
                        $niveau = $soute->$niveauKey ?? 0;
                        $pourcentage = ($capacite > 0) ? ($niveau / $capacite) * 100 : 0;
                        $pourcentage = round(min(max($pourcentage, 0), 100)); // S'assurer que c'est entre 0 et 100

                        $progressBarColor = 'bg-success'; // Vert par défaut
                        if ($pourcentage < 25) {
                            $progressBarColor = 'bg-danger'; // Rouge si < 25%
                        } elseif ($pourcentage < 50) {
                            $progressBarColor = 'bg-warning'; // Orange si < 50%
                        }
                    @endphp

                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    @if(strtolower($type) == 'diesel') <i class="bi bi-truck text-primary"></i>
                                    @elseif(strtolower($type) == 'kerozen') <i class="bi bi-airplane-engines-fill text-info"></i>
                                    @elseif(strtolower($type) == 'essence') <i class="bi bi-car-front-fill text-success"></i>
                                    @else <i class="bi bi-fuel-pump-fill text-secondary"></i>
                                    @endif
                                    {{ $type }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-1">Niveau Actuel : <strong>{{ number_format($niveau, 2, ',', ' ') }} L</strong></p>
                                <p class="text-muted mb-2">Capacité Totale : {{ number_format($capacite, 2, ',', ' ') }} L</p>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated {{ $progressBarColor }}"
                                         role="progressbar"
                                         style="width: {{ $pourcentage }}%;"
                                         aria-valuenow="{{ $pourcentage }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                        {{ $pourcentage }}%
                                    </div>
                                </div>
                                @if($capacite > 0 && $niveau > $capacite)
                                    <small class="text-danger d-block mt-1"><i class="bi bi-exclamation-triangle-fill"></i> Niveau actuel dépasse la capacité !</small>
                                @endif
                            </div>
                            <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                                <small class="text-muted">Remplissage : {{ $pourcentage }}%</small>
                                {{-- Tu pourrais ajouter un bouton d'action ici si pertinent --}}
                                {{-- <a href="#" class="btn btn-sm btn-outline-primary">Détails</a> --}}
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12">
                    <div class="alert alert-info text-center" role="alert">
                        Aucun type de carburant configuré pour cette soute ou informations de capacité manquantes.
                    </div>
                </div>
            @endif
        </div>
    </section>

    <section class="section mt-4">
        <div class="card">
            <div class="card-header">
                <h4>Actions Rapides</h4>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2 d-md-block">
                    {{-- Ce lien pointe vers le CRUD des transactions du Corps d'Armée.
                         Tu devras créer une interface spécifique pour le pompiste s'il ne doit voir/créer
                         que les transactions de SA soute. --}}
                    <a href="{{ route('corps.carburants.index', ['soute_id' => $soute->id]) }}" class="btn btn-primary">
                        <i class="bi bi-fuel-pump"></i> Enregistrer une Sortie de Carburant
                    </a>
                    {{-- <a href="#" class="btn btn-success">
                        <i class="bi bi-arrow-down-circle-fill"></i> Enregistrer une Entrée de Carburant
                    </a> --}}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts') {{-- Assure-toi que ton layout pompiste.layouts.app a @stack('scripts') --}}
  {{-- Si tu ajoutes des graphiques plus tard : --}}
  {{-- <script src="{{ asset('assets/plugins/chartjs/chart.min.js') }}"></script> --}}
  {{-- <script src="{{ asset('assets/js/dashboard.js') }}"></script> --}}
@endpush
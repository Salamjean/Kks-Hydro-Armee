{{-- resources/views/soute/dashboard.blade.php --}}

@extends('pompiste.layouts.app') {{-- <<--- MODIFIÉ pour utiliser le nouveau layout soute --}}

@section('title', 'Tableau de Bord - Soute ' . ($soute->nom ?? ''))

@section('content')
    {{-- ... (le contenu que tu avais déjà mis pour le dashboard soute) ... --}}
    {{-- Par exemple : --}}
    <div class="page-heading">
        <h3>Tableau de Bord Soute : {{ $soute->nom ?? 'Inconnue' }}</h3>
        <p class="text-subtitle text-muted">Géré par : {{ $personnel->nom_complet ?? 'Employé inconnu' }} (Matricule: {{ $personnel->matricule ?? '' }})</p>
    </div>

    <div class="page-content">
        <section class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Informations de la Soute</h4>
                    </div>
                    <div class="card-body">
                        <p>Matricule : <strong>{{ $soute->matricule_soute ?? 'N/A' }}</strong></p>
                        <p>Localisation : {{ $soute->localisation ?? 'N/A' }}</p>
                        <p>Type Carburant Principal : {{ $soute->type_carburant_principal ?? 'N/A' }}</p>
                        <p>Capacité Totale : {{ $soute->capacite_totale ? number_format($soute->capacite_totale, 2, ',', ' ') . ' L' : 'N/A' }}</p>
                        <p>Niveau Actuel (Global si géré) : {{ $soute->niveau_actuel_global ? number_format($soute->niveau_actuel_global, 2, ',', ' ') . ' L' : 'Non suivi à ce niveau' }}</p>

                        <h5 class="mt-4">Actions Rapides</h5>
                        <div class="d-grid gap-2 d-md-block">
                            <a href="{{ route('corps.carburants.index') }}" class="btn btn-primary">
                                <i class="bi bi-fuel-pump"></i> Enregistrer une Sortie de Carburant
                            </a>
                            {{-- <a href="#" class="btn btn-success">Enregistrer une Entrée de Carburant</a> --}}
                        </div>
                    </div>
                </div>
            </div>
             {{-- Tu pourrais ajouter ici des cartes pour le niveau des pompes (Distributeurs) de cette soute --}}
        </section>
    </div>
@endsection
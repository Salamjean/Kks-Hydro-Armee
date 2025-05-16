{{-- Exemple: resources/views/armee-terre/personnel/assign_soutes.blade.php --}}
{{-- Adapte le @extends à ton layout spécifique si besoin --}}
@extends('armee-terre.layouts.template')

@section('title', 'Assigner Soutes à ' . $personnel->nom_complet)

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Assigner/Modifier Soutes</h3>
                <p class="text-subtitle text-muted">Personnel : <strong>{{ $personnel->nom_complet }}</strong> (Matricule: {{ $personnel->matricule }})</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        @php
                            // Logique dynamique pour le lien du tableau de bord (copiée de ta vue index)
                            $breadcrumbDashboardRouteName = 'corps.gendarmerie.dashboard';
                            $authUser = Auth::guard('corps')->user();
                            if ($authUser) {
                                 $userCorpsName = $authUser->name;
                                 switch ($userCorpsName) {
                                    case 'Gendarmerie': $breadcrumbDashboardRouteName = 'corps.gendarmerie.dashboard'; break;
                                    case 'Marine': $breadcrumbDashboardRouteName = 'corps.marine.dashboard'; break;
                                    case 'Armée-Air': $breadcrumbDashboardRouteName = 'corps.armee-air.dashboard'; break;
                                    case 'Armée-Terre': $breadcrumbDashboardRouteName = 'corps.armee-terre.dashboard'; break;
                                 }
                            }
                        @endphp
                        <li class="breadcrumb-item"><a href="{{ route($breadcrumbDashboardRouteName) }}">Tableau de Bord</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('corps.personnel.index') }}">Personnel</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Assigner Soutes</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Erreurs :</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<section class="section">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Soutes pour {{ $personnel->nom_complet }}</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('corps.personnel.handleAssignSoutes', $personnel->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="soutes_assignment_ids" class="form-label">Sélectionner les soutes à assigner (maintenir CTRL/CMD pour sélection multiple) :</label>
                    <select name="soutes_ids[]" id="soutes_assignment_ids" class="form-select soute-select @error('soutes_ids') is-invalid @enderror @error('soutes_ids.*') is-invalid @enderror" multiple size="8">
                        @if($soutesDisponibles->count() > 0)
                            @foreach($soutesDisponibles as $soute)
                                <option value="{{ $soute->id }}" {{ in_array($soute->id, $soutesAssigneesIds) ? 'selected' : '' }}>
                                    {{ $soute->nom }} ({{ $soute->localisation ?? 'N/A' }}) - Matricule: {{ $soute->matricule_soute }}
                                </option>
                            @endforeach
                        @else
                            <option value="" disabled>Aucune soute disponible pour ce corps d'armée.</option>
                        @endif
                    </select>
                    @error('soutes_ids') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    @error('soutes_ids.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Enregistrer les Assignations</button>
                    <a href="{{ route('corps.personnel.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@push('styles') {{-- Assure-toi que ton layout a @stack('styles') --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #ced4da;
        min-height: 38px; /* Ajuste selon la hauteur de tes autres inputs */
    }
    /* Tu peux vouloir un peu plus de hauteur pour le select multiple */
    #soutes_assignment_ids {
        min-height: 150px;
    }
</style>
@endpush

@push('scripts') {{-- Assure-toi que ton layout a @stack('scripts') --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> {{-- Select2 a besoin de jQuery --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#soutes_assignment_ids').select2({
            placeholder: "Sélectionnez les soutes",
            width: '100%',
            allowClear: true // Permet de désélectionner toutes les options
        });
    });
</script>
@endpush
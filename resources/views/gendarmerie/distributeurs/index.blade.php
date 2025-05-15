@extends('gendarmerie.layouts.template')

@section('title', 'Liste des Distributeurs')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Liste des Distributeurs</h3>
                <p class="text-subtitle text-muted">Équipements de distribution (pompes, camions) rattachés à votre corps d'armée.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{-- Ajuster route dashboard --}}">Tableau de Bord</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Distributeurs</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

{{-- Affichage des messages de succès ou d'erreur --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if($errors->any() && old('form_type') === 'create_distributeur')
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Erreurs de validation :</strong>
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
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDistributeurModal">
                <i class="bi bi-plus-circle-fill"></i> Ajouter un Distributeur
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="tableDistributeurs">
                    <thead>
                        <tr>
                            <th>Identifiant</th>
                            <th>Type</th>
                            <th>Service de Rattachement</th>
                            <th>Capacité (L)</th>
                            <th>Niveau Actuel (L)</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($distributeurs as $distributeur)
                            <tr>
                                <td>{{ $distributeur->identifiant }}</td>
                                <td>{{ $distributeur->type }}</td>
                                <td>{{ $distributeur->service->nom ?? 'N/A' }}</td>
                                <td>{{ $distributeur->capacite ? number_format($distributeur->capacite, 2, ',', ' ') : 'N/A' }}</td>
                                <td>{{ $distributeur->niveau_actuel ? number_format($distributeur->niveau_actuel, 2, ',', ' ') : 'N/A' }}</td>
                                <td class="text-center">
                                    <a href="{{-- route('corps.distributeurs.edit', $distributeur->id) --}}#" class="btn btn-sm btn-info" title="Modifier">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" title="Supprimer"
                                            onclick="confirmDeleteDistributeur({{ $distributeur->id }}, '{{ $distributeur->identifiant }}')">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                    <form id="delete-distributeur-form-{{ $distributeur->id }}" action="{{-- route('corps.distributeurs.destroy', $distributeur->id) --}}#" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Aucun distributeur trouvé pour ce corps d'armée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $distributeurs->links() }}
            </div>
        </div>
    </div>
</section>

{{-- Modale de Création du Distributeur --}}
<div class="modal fade" id="createDistributeurModal" tabindex="-1" aria-labelledby="createDistributeurModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createDistributeurModalLabel">Ajouter un Nouveau Distributeur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('corps.distributeurs.store') }}" method="POST">
                @csrf
                <input type="hidden" name="form_type" value="create_distributeur">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="identifiant" class="form-label">Identifiant (Plaque, N° Pompe) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('identifiant') is-invalid @enderror" id="identifiant" name="identifiant" value="{{ old('identifiant') }}" required>
                        @error('identifiant') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Type de Distributeur <span class="text-danger">*</span></label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="">-- Sélectionner un type --</option>
                            <option value="Pompe Fixe" {{ old('type') == 'Pompe Fixe' ? 'selected' : '' }}>Pompe Fixe</option>
                            <option value="Camion Citerne" {{ old('type') == 'Camion Citerne' ? 'selected' : '' }}>Camion Citerne</option>
                            <option value="Fût Mobile" {{ old('type') == 'Fût Mobile' ? 'selected' : '' }}>Fût Mobile</option>
                            <option value="Autre" {{ old('type') == 'Autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="capacite" class="form-label">Capacité (en litres)</label>
                                <input type="number" step="0.01" class="form-control @error('capacite') is-invalid @enderror" id="capacite" name="capacite" value="{{ old('capacite') }}" placeholder="Ex: 5000.00">
                                @error('capacite') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                         <div class="col-md-6">
                            <div class="mb-3">
                                <label for="niveau_actuel" class="form-label">Niveau Actuel (en litres)</label>
                                <input type="number" step="0.01" class="form-control @error('niveau_actuel') is-invalid @enderror" id="niveau_actuel" name="niveau_actuel" value="{{ old('niveau_actuel') }}" placeholder="Optionnel">
                                @error('niveau_actuel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="dist_service_id" class="form-label">Service de rattachement <span class="text-danger">*</span></label>
                        <select class="form-select @error('service_id') is-invalid @enderror" id="dist_service_id" name="service_id" required>
                            <option value="">-- Sélectionner un service --</option>
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                    {{ $service->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('service_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer Distributeur</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDeleteDistributeur(distributeurId, distributeurIdentifiant) {
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Supprimer le distributeur '" + distributeurIdentifiant + "' ? Cette action est irréversible !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-distributeur-form-' + distributeurId).submit();
                }
            })
        }

        @if($errors->any() && old('form_type') === 'create_distributeur')
            document.addEventListener('DOMContentLoaded', function() {
                var createModal = new bootstrap.Modal(document.getElementById('createDistributeurModal'));
                createModal.show();
            });
        @endif
    </script>
@endsection
@extends('corpsArme.layouts.template')

@section('title', 'Gestion des Soutes')

@section('content')
{{-- ... (page-heading, messages de succès/erreur restent pareils) ... --}}

<section class="section">
    <div class="card">
        <div class="card-header">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSouteModal">
                <i class="bi bi-plus-lg"></i> Ajouter une Soute
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="tableSoutes">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Matricule Soute</th> {{-- <<--- NOUVELLE COLONNE --}}
                            <th>Localisation</th>
                            <th>Type Carburant Principal</th>
                            <th>Capacité (L)</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($soutes as $soute)
                            <tr>
                                <td>{{ $soute->nom }}</td>
                                <td>{{ $soute->matricule_soute }}</td> {{-- <<--- AFFICHER MATRICULE --}}
                                <td>{{ $soute->localisation ?? 'N/A' }}</td>
                                <td>{{ $soute->type_carburant_principal ?? 'N/A' }}</td>
                                <td>{{ $soute->capacite_totale ? number_format($soute->capacite_totale, 2, ',', ' ') : 'N/A' }}</td>
                                <td class="text-center">
                                    {{-- ... boutons actions ... --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Aucune soute trouvée.</td> {{-- colspan augmenté --}}
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $soutes->links() }}
            </div>
        </div>
    </div>
</section>

{{-- Modale de Création de Soute --}}
<div class="modal fade" id="createSouteModal" tabindex="-1" aria-labelledby="createSouteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createSouteModalLabel">Ajouter une Nouvelle Soute</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('corps.soutes.store') }}" method="POST">
                @csrf
                <input type="hidden" name="form_type" value="create_soute">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="soute_nom" class="form-label">Nom de la Soute <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nom') is-invalid @enderror" id="soute_nom" name="nom" value="{{ old('nom') }}" required>
                        @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    {{-- Le champ Matricule Soute n'est pas dans le formulaire de création car auto-généré --}}
                    <div class="mb-3">
                        <label for="soute_localisation" class="form-label">Localisation</label>
                        <input type="text" class="form-control @error('localisation') is-invalid @enderror" id="soute_localisation" name="localisation" value="{{ old('localisation') }}">
                        @error('localisation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="soute_type_carburant" class="form-label">Type Carburant Principal <span class="text-danger">*</span></label>
                            {{-- MODIFICATION : Champ Select --}}
                            <select class="form-select @error('type_carburant_principal') is-invalid @enderror" id="soute_type_carburant" name="type_carburant_principal" required>
                                <option value="">-- Sélectionner un type --</option>
                                <option value="Diesel" {{ old('type_carburant_principal') == 'Diesel' ? 'selected' : '' }}>Diesel</option>
                                <option value="Kerozen" {{ old('type_carburant_principal') == 'Kerozen' ? 'selected' : '' }}>Kérosène</option>
                                <option value="Essence" {{ old('type_carburant_principal') == 'Essence' ? 'selected' : '' }}>Essence</option>
                            </select>
                            @error('type_carburant_principal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="soute_capacite" class="form-label">Capacité Totale (Litres)</label>
                                <input type="number" step="0.01" class="form-control @error('capacite_totale') is-invalid @enderror" id="soute_capacite" name="capacite_totale" value="{{ old('capacite_totale') }}">
                                @error('capacite_totale') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="soute_description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="soute_description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer Soute</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDeleteSoute(souteId, souteName) {
        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: "Supprimer la soute '" + souteName + "' ? Cette action est irréversible et pourrait affecter le personnel et les distributeurs liés !",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer !',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                // Décommentez et ajustez la route lorsque la suppression sera implémentée
                // document.getElementById('delete-soute-form-' + souteId).submit();
            }
        })
    }

    @if($errors->any() && old('form_type') === 'create_soute')
        document.addEventListener('DOMContentLoaded', function() {
            var createModal = new bootstrap.Modal(document.getElementById('createSouteModal'));
            createModal.show();
        });
    @endif
</script>
@endsection
@extends('armee-terre.layouts.template')

@section('title', 'Gestion des Soutes')

@section('content')
{{-- ... (page-heading, messages) ... --}}

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
                            <th>Matricule Soute</th>
                            <th>Localisation</th>
                            <th>Types Carburant & Capacités (L)</th> {{-- Colonne Modifiée --}}
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($soutes as $soute)
                            <tr>
                                <td>{{ $soute->nom }}</td>
                                <td>{{ $soute->matricule_soute }}</td>
                                <td>{{ $soute->localisation ?? 'N/A' }}</td>
                                <td> {{-- Affichage des types et capacités --}}
                                    @if(!empty($soute->types_carburants_stockes))
                                        @foreach($soute->types_carburants_stockes as $type)
                                            <div>
                                                <strong>{{ $type }}:</strong>
                                                @if($type == 'Diesel' && $soute->capacite_diesel)
                                                    {{ number_format($soute->capacite_diesel, 0, ',', ' ') }} L
                                                @elseif($type == 'Kerozen' && $soute->capacite_kerozen)
                                                    {{ number_format($soute->capacite_kerozen, 0, ',', ' ') }} L
                                                @elseif($type == 'Essence' && $soute->capacite_essence)
                                                    {{ number_format($soute->capacite_essence, 0, ',', ' ') }} L
                                                @else
                                                    N/A
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        Aucun type défini
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{-- ... boutons actions ... --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Aucune soute trouvée.</td> {{-- colspan ajusté --}}
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- ... (pagination) ... --}}
        </div>
    </div>
</section>

{{-- Modale de Création de Soute --}}
<div class="modal fade" id="createSouteModal" tabindex="-1" aria-labelledby="createSouteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> {{-- Agrandir la modale --}}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createSouteModalLabel">Ajouter une Nouvelle Soute</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
            </div>
            <form action="{{ route('corps.soutes.store') }}" method="POST">
                @csrf
                <input type="hidden" name="form_type" value="create_soute">
                <div class="modal-body">
                     <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="soute_nom" class="form-label">Nom de la Soute <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" id="soute_nom" name="nom" value="{{ old('nom') }}" required>
                            @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="soute_localisation" class="form-label">Localisation</label>
                            <input type="text" class="form-control @error('localisation') is-invalid @enderror" id="soute_localisation" name="localisation" value="{{ old('localisation') }}">
                            @error('localisation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                   </div>

                    <hr>
                    <h6>Capacités par Type de Carburant</h6>

                    <div class="mb-3">
                        <label class="form-label">Types de Carburant Stockés <span class="text-danger">*</span></label>
                        <div class="d-flex flex-wrap gap-3">
                            @php
                                $oldCarburants = old('type_carburants', []); // Récupère les anciennes valeurs cochées
                            @endphp
                            @foreach(['Diesel', 'Kerozen', 'Essence'] as $type)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('type_carburants') is-invalid @enderror"
                                           type="checkbox"
                                           name="type_carburants[]" {{-- Nom en tableau pour sélection multiple --}}
                                           id="carburant_{{ strtolower(str_replace(' ', '_', $type)) }}"
                                           value="{{ $type }}"
                                           {{ in_array($type, $oldCarburants) ? 'checked' : '' }}
                                           onchange="toggleCapacityInput('{{ strtolower(str_replace(' ', '_', $type)) }}')">
                                    <label class="form-check-label" for="carburant_{{ strtolower(str_replace(' ', '_', $type)) }}">
                                        {{ $type }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('type_carburants') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        @error('type_carburants.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    {{-- Champs de capacité conditionnels --}}
                    <div class="row">
                        <div class="col-md-4 mb-3" id="capacity_diesel_container" style="{{ in_array('Diesel', $oldCarburants) ? '' : 'display:none;' }}">
                            <div>
                                <label for="soute_capacite_diesel" class="form-label">Capacité Diesel (L)</label>
                                <input type="number" step="any" class="form-control @error('capacite_diesel') is-invalid @enderror"
                                    id="soute_capacite_diesel" name="capacite_diesel" value="{{ old('capacite_diesel') }}" placeholder="Ex: 5000">
                                @error('capacite_diesel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label for="soute_capacite_diesel" class="form-label">Capacité disponible (L)</label>
                                <input type="number" step="any" class="form-control @error('capacite_disponible') is-invalid @enderror"
                                    id="soute_capacite_diesel" name="niveau_actuel_diesel" value="{{ old('capacite_diesel') }}" placeholder="Ex: 5000">
                                @error('capacite_disponible') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label for="soute_capacite_diesel" class="form-label">Seuil d'alert (L)</label>
                                <input type="number" step="any" class="form-control @error('seuil_alert') is-invalid @enderror"
                                    id="soute_capacite_diesel" name="seuil_alert_diesel" value="{{ old('seuil_alert') }}" placeholder="Ex: 5000">
                                @error('seuil_alert') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label for="soute_capacite_diesel" class="form-label">Seuil d'insdisponibilité (L)</label>
                                <input type="number" step="any" class="form-control @error('seuil_indisponibilite_diesel') is-invalid @enderror"
                                    id="soute_capacite_diesel" name="seuil_indisponibilite_diesel" value="{{ old('seuil_indisponibilite_diesel') }}" placeholder="Ex: 5000">
                                @error('seuil_indisponibilite_diesel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4 mb-3" id="capacity_kerozen_container" style="{{ in_array('Kerozen', $oldCarburants) ? '' : 'display:none;' }}">
                            <div>
                                <label for="soute_capacite_kerozen" class="form-label">Capacité Kérosène (L)</label>
                                <input type="number" step="0.01" class="form-control @error('capacite_kerozen') is-invalid @enderror"
                                    id="soute_capacite_kerozen" name="capacite_kerozen" value="{{ old('capacite_kerozen') }}" placeholder="Ex: 3000">
                                @error('capacite_kerozen') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>  
                            <div>
                                <label for="soute_capacite_kerozen" class="form-label">Capacité disponible (L)</label>
                                <input type="number" step="0.01" class="form-control @error('capacite_disponible') is-invalid @enderror"
                                    id="soute_capacite_kerozen" name="niveau_actuel_kerozen" value="{{ old('capacite_disponible') }}" placeholder="Ex: 3000">
                                @error('capacite_disponible') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>  
                            <div>
                                <label for="soute_capacite_kerozen" class="form-label">Seuil d'alert (L)</label>
                                <input type="number" step="0.01" class="form-control @error('seuil_alert') is-invalid @enderror"
                                    id="soute_capacite_kerozen" name="seuil_alert_kerozen" value="{{ old('seuil_alert') }}" placeholder="Ex: 3000">
                                @error('seuil_alert') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>  
                            <div>
                                <label for="soute_capacite_kerozen" class="form-label">Seuil d'insdisponibilité (L)</label>
                                <input type="number" step="any" class="form-control @error('seuil_indisponibilite_kerozen') is-invalid @enderror"
                                    id="soute_capacite_kerozen" name="seuil_indisponibilite_kerozen" value="{{ old('seuil_indisponibilite_kerozen') }}" placeholder="Ex: 5000">
                                @error('seuil_indisponibilite_kerozen') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4 mb-3" id="capacity_essence_container" style="{{ in_array('Essence', $oldCarburants) ? '' : 'display:none;' }}">
                           <div>
                                <label for="soute_capacite_essence" class="form-label">Capacité Essence (L)</label>
                                <input type="number" step="0.01" class="form-control @error('capacite_essence') is-invalid @enderror"
                                    id="soute_capacite_essence" name="capacite_essence" value="{{ old('capacite_essence') }}" placeholder="Ex: 2000">
                                @error('capacite_essence') <div class="invalid-feedback">{{ $message }}</div> @enderror
                           </div>
                            <div>
                                <label for="soute_capacite_essence" class="form-label">Capacité disponible (L)</label>
                                <input type="number" step="0.01" class="form-control @error('capacite_essence') is-invalid @enderror"
                                    id="soute_capacite_essence" name="niveau_actuel_essence" value="{{ old('capacite_disponible') }}" placeholder="Ex: 2000">
                                @error('capacite_disponible') <div class="invalid-feedback">{{ $message }}</div> @enderror
                           </div>
                            <div>
                                <label for="soute_capacite_essence" class="form-label">Seuil d'alert (L)</label>
                                <input type="number" step="0.01" class="form-control @error('capacite_alert') is-invalid @enderror"
                                    id="soute_capacite_essence" name="seuil_alert_essence" value="{{ old('capacite_alert') }}" placeholder="Ex: 2000">
                                @error('capacite_alert') <div class="invalid-feedback">{{ $message }}</div> @enderror
                           </div>
                           <div>
                                <label for="soute_capacite_essence" class="form-label">Seuil d'insdisponibilité (L)</label>
                                <input type="number" step="any" class="form-control @error('seuil_indisponibilite_essence') is-invalid @enderror"
                                    id="soute_capacite_essence" name="seuil_indisponibilite_essence" value="{{ old('seuil_indisponibilite_essence') }}" placeholder="Ex: 5000">
                                @error('seuil_indisponibilite_essence') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- <div class="mb-3">
                        <label for="soute_description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="soute_description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div> -->
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
@push('custom-scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($errors->hasBag('default') && old('form_type') === 'create_soute')
            var createModal = new bootstrap.Modal(document.getElementById('createSouteModal'));
            createModal.show();
        @endif
    });
     // Script pour afficher/cacher les champs de capacité en fonction des cases cochées
     function toggleCapacityInput(fuelType) {
            const checkbox = document.getElementById('carburant_' + fuelType);
            const container = document.getElementById('capacity_' + fuelType + '_container');
            if (checkbox && container) {
                container.style.display = checkbox.checked ? 'block' : 'none';
                if (!checkbox.checked) {
                    // Optionnel: vider le champ de capacité si la case est décochée
                    const input = container.querySelector('input[type="number"]');
                    if (input) input.value = '';
                }
            }
        }

        // Attacher les écouteurs d'événements aux checkboxes
        ['diesel', 'kerozen', 'essence'].forEach(type => {
            const checkbox = document.getElementById('carburant_' + type);
            if (checkbox) {
                checkbox.addEventListener('change', function() {
                    toggleCapacityInput(type);
                });
                // Initialiser l'état au chargement de la page (utile si old() a coché des cases)
                // toggleCapacityInput(type); // Déjà géré par le style inline basé sur old()
            }
        });

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
                console.log("Suppression confirmée pour la soute ID: " + souteId);
            }
        })
    }
</script>
@endpush

<style>
    #createSouteModal .modal-dialog {
    max-width: 800px;
}

.custom-select-style {
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
    border-radius: 0.5rem;
    padding: 0.6rem 1rem;
    font-size: 1rem;
    color: #212529;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.custom-select-style:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    outline: none;
}

.custom-select-style.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.custom-select-style option:disabled {
    color: #6c757d;
}

.custom-select-style option:hover {
    background-color: #e9ecef;
}
</style>
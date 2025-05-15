@extends('corpsArme.layouts.template')

@section('title', 'Gestion des Soutes')

@section('content')

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
                            <th>Type Carburant Principal</th>
                            <th>Capacité (L)</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($soutes as $soute)
                            <tr>
                                <td>{{ $soute->nom }}</td>
                                <td>{{ $soute->matricule_soute }}</td>
                                <td>{{ $soute->localisation ?? 'N/A' }}</td>
                                <td>{{ $soute->type_carburant_principal ?? 'N/A' }}</td>
                                <td>{{ $soute->capacite_totale ? number_format($soute->capacite_totale, 2, ',', ' ') : 'N/A' }}</td>
                                <td class="text-center">
                                
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Aucune soute trouvée.</td>
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

<div class="modal fade" id="createSouteModal" tabindex="-1" aria-labelledby="createSouteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createSouteModalLabel">Ajouter une Nouvelle Soute</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> x</button>
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

                    <div class="mb-3">
                        <label for="soute_localisation" class="form-label">Localisation</label>
                        <input type="text" class="form-control @error('localisation') is-invalid @enderror" id="soute_localisation" name="localisation" value="{{ old('localisation') }}">
                        @error('localisation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Types de Carburant Principaux <span class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap gap-3">
                                @php
                                    $oldCarburants = old('type_carburants', []);
                                @endphp
                        
                                @foreach(['Diesel', 'Kerozen', 'Essence'] as $type)
                                    <div class="form-check">
                                        <input class="form-check-input @error('type_carburants') is-invalid @enderror"
                                               type="checkbox"
                                               name="type_carburants[]"
                                               id="carburant_{{ $type }}"
                                               value="{{ $type }}"
                                               {{ in_array($type, $oldCarburants) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="carburant_{{ $type }}">
                                            {{ $type }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('type_carburants') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div> 

                        <div class="col-md-2 mb-3">
                            <label for="soute_capacite" class="form-label">Capacité Diesel</label>
                            <input type="number" step="0.01" class="form-control @error('capacite_totale') is-invalid @enderror" id="soute_capacite" name="capacite_totale" value="{{ old('capacite_totale') }}" placeholder="Litre">
                            @error('capacite_totale') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="soute_capacite" class="form-label">Capacité Kerozen </label>
                            <input type="number" step="0.01" class="form-control @error('capacite_totale') is-invalid @enderror" id="soute_capacite" name="capacite_totale" value="{{ old('capacite_totale') }}" placeholder="Litre">
                            @error('capacite_totale') 
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="soute_capacite" class="form-label">Capacité Essance</label>
                            <input type="number" step="0.01" class="form-control @error('capacite_totale') is-invalid @enderror" id="soute_capacite" name="capacite_totale" value="{{ old('capacite_totale') }}" placeholder="Litre">
                            @error('capacite_totale') 
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- Ce modal-footer est correctement placé --}}
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
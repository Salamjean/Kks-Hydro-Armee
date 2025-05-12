@extends('corpsArme.layouts.template')

@section('title', 'Gestion des Soutes')

@section('content')

<section class="section">
    <div class="card">
        <div class="card-header">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSouteModal">
                <i class="bi bi-plus-lg"></i> Ajouter un Employé
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="tablePersonnel">
                    <thead>
                        <tr>
                            <th>Matricule</th>
                            <th>Nom Complet</th>
                            <th>Email</th>
                            <th>Soute</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($personnels as $personnel)
                            <tr>
                                <td>{{ $personnel->matricule }}</td>
                                <td>{{ $personnel->nom_complet }}</td>
                                <td>{{ $personnel->email ?? 'N/A' }}</td>
                                <td>{{ $personnel->soute->nom ?? 'Aucune soute' }}</td>
                                <td class="text-center">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Aucun personnel trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $personnels->links() }}
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="createSouteModal" tabindex="-1" aria-labelledby="createSouteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createPersonnelModalLabel">Ajouter un Nouvel Employé</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
            </div>
            <form action="{{ route('corps.personnel.store') }}" method="POST">
                @csrf
                <input type="hidden" name="form_type" value="create_personnel">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('prenom', 'create_personnel_form') is-invalid @enderror" id="prenom" name="prenom" value="{{ old('prenom') }}" required>
                            @error('prenom', 'create_personnel_form') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3"> 
                            <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom', 'create_personnel_form') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom') }}" required>
                            @error('nom', 'create_personnel_form') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="matricule" class="form-label">Matricule <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('matricule', 'create_personnel_form') is-invalid @enderror" id="matricule" name="matricule" value="{{ old('matricule') }}" required>
                            @error('matricule', 'create_personnel_form') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email', 'create_personnel_form') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                            @error('email', 'create_personnel_form') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="soute_id" class="form-label">Soute de rattachement</label>
                        <select class="form-select custom-select-style @error('soute_id', 'create_personnel_form') is-invalid @enderror" id="soute_id" name="soute_id">
                            <option value="">-- Sélectionner une soute (Optionnel) --</option>
                            @if(isset($soutes) && $soutes->count() > 0)
                                @foreach ($soutes as $soute)
                                    <option value="{{ $soute->id }}" {{ old('soute_id') == $soute->id ? 'selected' : '' }}>
                                        {{ $soute->nom }} ({{ $soute->localisation ?? 'N/L' }})
                                    </option>
                                @endforeach
                            @else
                                <option value="" disabled>Aucune soute disponible</option>
                            @endif
                        </select>
                        @error('soute_id', 'create_personnel_form') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer Employé</button>
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


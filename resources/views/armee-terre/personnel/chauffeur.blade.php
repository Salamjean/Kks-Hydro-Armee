@extends('armee-terre.layouts.template')

@section('title', 'Gestion du Personnel')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Gestion des Chauffeurs</h3>
                <p class="text-subtitle text-muted">Liste des Chauffeurs.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('corps.dashboard') }}">Tableau de Bord</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Personnel</li>
                        <li class="breadcrumb-item active" aria-current="page">Chauffeurs</li>
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
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPersonnelModal">
                <i class="bi bi-person-plus-fill"></i> Ajouter Chauffeurs
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
                            <th>Soute(s)</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($personnels as $personnel)
                            <tr>
                                <td>{{ $personnel->matricule }}</td>
                                <td>{{ $personnel->nom_complet }}</td>
                                <td>{{ $personnel->email ?? 'N/A' }}</td>
                                <td>
                                    @foreach($personnel->soutes as $soute)
                                        <span class="badge bg-primary">{{ $soute->nom }}</span>
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-info edit-btn"
                                            data-id="{{ $personnel->id }}"
                                            data-nom="{{ $personnel->nom }}"
                                            data-prenom="{{ $personnel->prenom }}"
                                            data-matricule="{{ $personnel->matricule }}"
                                            data-email="{{ $personnel->email }}"
                                            data-soutes="{{ $personnel->soutes->pluck('id')->toJson() }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-btn"
                                            data-id="{{ $personnel->id }}"
                                            data-name="{{ $personnel->nom_complet }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Aucun Chauffeurs trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $personnels->links() }}
        </div>
    </div>
</section>

<!-- Modale de création -->
<div class="modal fade" id="createPersonnelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un Chauffeurs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
            </div>
            <form action="{{ route('corps.personnel.store') }}" method="POST">
            <input type="hidden" name="form_type" value="create"> 
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nom *</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Prénom *</label>
                            <input type="text" name="prenom" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Matricule *</label>
                            <input type="text" name="matricule" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Immatriculation du Véhicule *</label>
                        <input type="text" name="matricule" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modale d'édition -->
<div class="modal fade" id="editPersonnelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier l'Employé</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Le contenu est rempli par JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modale de suppression -->
<div class="modal fade" id="deletePersonnelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer <span id="personnelName"></span> ?</p>
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #ced4da;
        min-height: 38px;
    }
    .badge {
        margin-right: 5px;
    }
</style>
@endpush

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
    document.addEventListener('DOMContentLoaded', function() {
    // Gestion de l'édition
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const personnel = {
                id: this.dataset.id,
                nom: this.dataset.nom,
                prenom: this.dataset.prenom,
                matricule: this.dataset.matricule,
                email: this.dataset.email,
                soutes: JSON.parse(this.dataset.soutes)
            };

            // Mise à jour du formulaire
            const editForm = document.getElementById('editForm');
            editForm.action = `/corps/personnel/${personnel.id}`;

            // Génération des options
            let optionsHtml = '';
            @foreach($soutes as $soute)
                const selected = personnel.soutes.includes({{ $soute->id }}) ? 'selected' : '';
                optionsHtml += `<option value="{{ $soute->id }}" ${selected}>{{ $soute->nom }}</option>`;
            @endforeach

            // Injection HTML
            document.querySelector('#editPersonnelModal .modal-body').innerHTML = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Prénom *</label>
                        <input type="text" name="prenom" class="form-control" value="${personnel.prenom}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nom *</label>
                        <input type="text" name="nom" class="form-control" value="${personnel.nom}" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Matricule *</label>
                        <input type="text" name="matricule" class="form-control" value="${personnel.matricule}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="${personnel.email}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Soutes associées</label>
                    <select name="soutes_ids[]" class="form-select soute-select-edit" multiple>
                        ${optionsHtml}
                    </select>
                </div>
            `;

            // Initialisation Select2
            $('.soute-select-edit').select2({
                placeholder: "Sélectionnez des soutes",
                width: '100%'
            });

            // Affichage de la modale
            new bootstrap.Modal(document.getElementById('editPersonnelModal')).show();
        });
    });

    // Gestion suppression
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('personnelName').textContent = this.dataset.name;
            document.getElementById('deleteForm').action = `/corps/personnel/${this.dataset.id}`;
            new bootstrap.Modal(document.getElementById('deletePersonnelModal')).show();
        });
    });
});
</script>
@endpush

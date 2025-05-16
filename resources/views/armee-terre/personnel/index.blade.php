@extends('armee-terre.layouts.template')

@section('title', 'Gestion du Personnel')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Gestion des Pompistes</h3>
                <p class="text-subtitle text-muted">Liste des Pompistes.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('corps.armee-terre.dashboard') }}">Tableau de Bord</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Personnel</li>
                        <li class="breadcrumb-item active" aria-current="page">Pompiste</li>
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
                <i class="bi bi-person-plus-fill"></i> Ajouter Pompiste
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
                                     {{-- NOUVEAU BOUTON : Assigner Soutes --}}
    <a href="{{ route('corps.personnel.assignSoutesForm', $personnel->id) }}" 
        class="btn btn-sm btn-warning" 
        title="Assigner/Gérer les soutes">
         <i class="bi bi-archive-fill"></i> {{-- Ou une autre icône pertinente --}}
     </a>
                                    <button type="button" class="btn btn-sm btn-danger delete-btn"
                                            data-id="{{ $personnel->id }}"
                                            data-name="{{ $personnel->nom_complet }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Aucun personnel trouvé</td>
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
                <h5 class="modal-title">Ajouter un Employé</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
            </div>
            <form action="{{ route('corps.personnel.store') }}" method="POST">
            <input type="hidden" name="form_type" value="create"> 
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Prénom *</label>
                            <input type="text" name="prenom" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nom *</label>
                            <input type="text" name="nom" class="form-control" required>
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
                    <div class="mb-3">
                        <label class="form-label">Soutes associées</label>
                        <select name="soutes_ids[]" class="form-select soute-select" multiple>
                            @foreach($soutes as $soute)
                                <option value="{{ $soute->id }}">{{ $soute->nom }}</option>
                            @endforeach
                        </select>
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
{{-- jQuery, Bootstrap JS, Select2 JS, SweetAlert2 --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.jQuery) {
        if (document.querySelector('#createPersonnelModal .soute-select')) {
            $('#createPersonnelModal .soute-select').select2({
                placeholder: "Sélectionnez des soutes",
                width: '100%',
                dropdownParent: $('#createPersonnelModal')
            });
        }
    } else {
        console.error("jQuery n'est pas chargé. Select2 ne fonctionnera pas.");
    }

    @if($errors->any() && old('form_type') === 'create')
        var createModalEl = document.getElementById('createPersonnelModal');
        if (createModalEl) {
            var createModalInstance = bootstrap.Modal.getInstance(createModalEl) || new bootstrap.Modal(createModalEl);
            createModalInstance.show();
        }
    @endif

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const personnelId = this.dataset.id;
            const nom = this.dataset.nom;
            const prenom = this.dataset.prenom;
            const matricule = this.dataset.matricule;
            const email = this.dataset.email || '';
            // const serviceId = this.dataset.service_id; // RETIRER
            const selectedSoutesIds = JSON.parse(this.dataset.soutes || '[]');

            const editForm = document.getElementById('editForm');
            if (!editForm) {
                console.error("Le formulaire d'édition #editForm n'a pas été trouvé.");
                return;
            }
            editForm.action = `{{ url('corps/personnel') }}/${personnelId}`;

            let souteOptionsHtml = '';
            const allSoutes = @json($soutes); // $soutes doit toujours être passé par le contrôleur
            allSoutes.forEach(soute => {
                const selected = selectedSoutesIds.includes(soute.id) ? 'selected' : '';
                souteOptionsHtml += `<option value="${soute.id}" ${selected}>${soute.nom}</option>`;
            });

            // let serviceOptionsHtml = '<option value="">-- Sélectionner un service --</option>'; // RETIRER
            // const allServices = @json($services); // RETIRER si $services n'est plus passé
            // allServices.forEach(service => { // RETIRER
            //     const selected = serviceId == service.id ? 'selected' : ''; // RETIRER
            //     serviceOptionsHtml += `<option value="${service.id}" ${selected}>${service.nom}</option>`; // RETIRER
            // }); // RETIRER

            const modalBody = document.querySelector('#editPersonnelModal .modal-body');
            if (!modalBody) {
                console.error("Le corps de la modale d'édition n'a pas été trouvé.");
                return;
            }
            modalBody.innerHTML = `
                <input type="hidden" name="id" value="${personnelId}">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Prénom *</label>
                        <input type="text" name="prenom" class="form-control" value="${prenom}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nom *</label>
                        <input type="text" name="nom" class="form-control" value="${nom}" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Matricule *</label>
                        <input type="text" name="matricule" class="form-control" value="${matricule}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="${email}">
                    </div>
                </div>
                {{-- <div class="mb-3"> RETIRER LE BLOC SERVICE
                    <label class="form-label">Service</label>
                    <select name="service_id" class="form-select">
                        ${serviceOptionsHtml}
                    </select>
                </div> --}}
                <div class="mb-3">
                    <label class="form-label">Soutes associées</label>
                    <select name="soutes_ids[]" class="form-select soute-select-edit" multiple>
                        ${souteOptionsHtml}
                    </select>
                </div>
            `;

            if (window.jQuery) {
                const selectEditElement = $('#editPersonnelModal .soute-select-edit');
                if (selectEditElement.length) {
                    if (selectEditElement.data('select2')) {
                        selectEditElement.select2('destroy');
                    }
                    selectEditElement.select2({
                        placeholder: "Sélectionnez des soutes",
                        width: '100%',
                        dropdownParent: $('#editPersonnelModal')
                    });
                }
            } else {
                console.error("jQuery n'est pas chargé. Select2 dans la modale d'édition ne fonctionnera pas.");
            }

            var editModalEl = document.getElementById('editPersonnelModal');
            if (editModalEl) {
                var editModalInstance = bootstrap.Modal.getInstance(editModalEl) || new bootstrap.Modal(editModalEl);
                editModalInstance.show();
            }
        });
    });
    // Gestion suppression
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const personnelId = this.dataset.id;
            const personnelName = this.dataset.name;

            const personnelNameSpan = document.getElementById('personnelName');
            if (personnelNameSpan) personnelNameSpan.textContent = personnelName;

            const deleteForm = document.getElementById('deleteForm');
            if (!deleteForm) {
                console.error("Le formulaire de suppression #deleteForm n'a pas été trouvé.");
                return;
            }
            deleteForm.action = `{{ url('corps/personnel') }}/${personnelId}`;

            var deleteModalEl = document.getElementById('deletePersonnelModal');
            if (deleteModalEl) {
                var deleteModalInstance = bootstrap.Modal.getInstance(deleteModalEl) || new bootstrap.Modal(deleteModalEl);
                deleteModalInstance.show();
            }
        });
    });
});
</script>
@endpush

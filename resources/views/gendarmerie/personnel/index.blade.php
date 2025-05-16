@extends('gendarmerie.layouts.template')


@section('title', 'Gestion du Personnel')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--bootstrap-5 .select2-dropdown {
            z-index: 1060; 
        }
    </style>
@endpush

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Gestion du personnel</h3>
                <p class="text-subtitle text-muted">Liste du personnel.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('corps.armee-air.dashboard') }}">Tableau de Bord</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Logistique</li>
                        <li class="breadcrumb-item active" aria-current="page">Personnel</li>
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
                <i class="bi bi-person-plus-fill"></i> Ajouter un personnel
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
                            <th>Soute(s) Affectée(s)</th>
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
                                    @forelse($personnel->soutes as $soute)
                                        <span class="badge bg-info text-dark me-1">{{ $soute->nom }}</span>
                                    @empty
                                        <span class="badge bg-light text-dark">Aucune</span>
                                    @endforelse
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-info edit-btn"
                                            title="Modifier {{ $personnel->nom_complet }}"
                                            data-id="{{ $personnel->id }}"
                                            data-nom="{{ $personnel->nom }}"
                                            data-prenom="{{ $personnel->prenom }}"
                                            data-matricule="{{ $personnel->matricule }}"
                                            data-email="{{ $personnel->email ?? '' }}"
                                            data-soutes="{{ $personnel->soutes->pluck('id')->toJson() }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-btn"
                                            title="Supprimer {{ $personnel->nom_complet }}"
                                            data-id="{{ $personnel->id }}"
                                            data-name="{{ $personnel->nom_complet }}">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
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
            @if ($personnels->hasPages())
                <div class="mt-3">
                    {{ $personnels->links() }}
                </div>
            @endif
        </div>
    </div>
</section>

<div class="modal fade" id="createPersonnelModal" tabindex="-1" aria-labelledby="createPersonnelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createPersonnelModalLabel">Ajouter un nouveau personnel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">X</button>
            </div>
            <form action="{{ route('corps.personnel.store') }}" method="POST" id="createPersonnelForm">
                @csrf
                
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="create_prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="prenom" id="create_prenom" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="create_nom" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="nom" id="create_nom" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="create_matricule" class="form-label">Matricule <span class="text-danger">*</span></label>
                            <input type="text" name="matricule" id="create_matricule" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="create_email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="create_email" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="create_soutes_ids" class="form-label">Soutes associées</label>
                            <select name="soutes_ids[]" id="create_soutes_ids" class="form-select select2-multiple" multiple="multiple" style="width: 100%;">
                                @foreach($soutes as $soute)
                                    <option value="{{ $soute->id }}">{{ $soute->nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="create_role" class="form-label">Rôle(s) <span class="text-danger">*</span></label>
                            <select name="role[]" id="create_role" class="form-select select2-multiple" multiple="multiple" style="width: 100%;" required>
                                <option value="Pompiste">Pompiste</option>
                                <option value="Gestionnaire">Gestionnaire</option>
                                <option value="Superviseur">Superviseur</option>
                            </select>
                        </div>
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


<div class="modal fade" id="editPersonnelModal" tabindex="-1" aria-labelledby="editPersonnelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-dark">
                <h5 class="modal-title" id="editPersonnelModalLabel">Modifier les informations du personnel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPersonnelForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                     <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="prenom" id="edit_prenom" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_nom" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="nom" id="edit_nom" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_matricule" class="form-label">Matricule <span class="text-danger">*</span></label>
                            <input type="text" name="matricule" id="edit_matricule" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_soutes_ids" class="form-label">Soutes associées</label>
                            <select name="soutes_ids[]" id="edit_soutes_ids" class="form-select select2-multiple" multiple="multiple" style="width: 100%;">
                                @foreach($soutes as $soute)
                                    <option value="{{ $soute->id }}">{{ $soute->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                             <label for="edit_role" class="form-label">Rôle(s) <span class="text-danger">*</span></label>
                            <select name="role[]" id="edit_role" class="form-select select2-multiple" multiple="multiple" style="width: 100%;" required>
                                <option value="Pompiste">Pompiste</option>
                                <option value="Gestionnaire">Gestionnaire</option>
                                <option value="Superviseur">Superviseur</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deletePersonnelModal" tabindex="-1" aria-labelledby="deletePersonnelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deletePersonnelModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deletePersonnelForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer le personnel <strong id="personnelNameToDelete"></strong> ?</p>
                    <p class="text-danger">Cette action est irréversible.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('custom-scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const createModalEl = document.getElementById('createPersonnelModal');
    const editModalEl = document.getElementById('editPersonnelModal');
    const deleteModalEl = document.getElementById('deletePersonnelModal');

    const createModal = new bootstrap.Modal(createModalEl);
    const editModal = new bootstrap.Modal(editModalEl);
    const deleteModal = new bootstrap.Modal(deleteModalEl);

    $('#create_soutes_ids').select2({
        dropdownParent: $('#createPersonnelModal'), 
        placeholder: "Sélectionnez une ou plusieurs soutes",
        allowClear: true,
        width: '100%'
    });
    $('#create_role').select2({
        dropdownParent: $('#createPersonnelModal'),
        placeholder: "Sélectionnez un ou plusieurs rôles",
        allowClear: true,
        width: '100%'
    });

    $('#edit_soutes_ids').select2({
        dropdownParent: $('#editPersonnelModal'),
        placeholder: "Sélectionnez une ou plusieurs soutes",
        allowClear: true,
        width: '100%'
    });
     $('#edit_role').select2({
        dropdownParent: $('#editPersonnelModal'),
        placeholder: "Sélectionnez un ou plusieurs rôles",
        allowClear: true,
        width: '100%'
    });


    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const personnelId = this.dataset.id;
            const personnelNom = this.dataset.nom;
            const personnelPrenom = this.dataset.prenom;
            const personnelMatricule = this.dataset.matricule;
            const personnelEmail = this.dataset.email;
            const personnelSoutes = JSON.parse(this.dataset.soutes || '[]');
           
            const editForm = document.getElementById('editPersonnelForm');
            editForm.action = `{{ url('corps/personnel') }}/${personnelId}`;

            document.getElementById('edit_id').value = personnelId;
            document.getElementById('edit_prenom').value = personnelPrenom;
            document.getElementById('edit_nom').value = personnelNom;
            document.getElementById('edit_matricule').value = personnelMatricule;
            document.getElementById('edit_email').value = personnelEmail;
            
            $('#edit_soutes_ids').val(personnelSoutes).trigger('change');

            document.getElementById('editPersonnelModalLabel').textContent = `Modifier ${personnelPrenom} ${personnelNom}`;
            editModal.show();
        });
    });

    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const personnelId = this.dataset.id;
            const personnelName = this.dataset.name;

            const deleteForm = document.getElementById('deletePersonnelForm');
            deleteForm.action = `{{ url('corps/personnel') }}/${personnelId}`;

            document.getElementById('personnelNameToDelete').textContent = personnelName;
            deleteModal.show();
        });
    });

    createModalEl.addEventListener('hidden.bs.modal', function () {
        document.getElementById('createPersonnelForm').reset();
        $('#create_soutes_ids').val(null).trigger('change');
        $('#create_role').val(null).trigger('change');
    });
});
</script>
@endpush
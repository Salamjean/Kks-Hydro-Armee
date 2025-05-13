@extends('corpsArme.layouts.template')

@section('title', 'Gestion du Personnel')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Gestion du Personnel</h3>
                <p class="text-subtitle text-muted">Liste des employés de votre corps d'armée.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('corps.dashboard') }}">Tableau de Bord</a></li>
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
                <i class="bi bi-person-plus-fill"></i> Ajouter un Employé
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

<div class="modal fade" id="createPersonnelModal" tabindex="-1" aria-labelledby="createPersonnelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createPersonnelModalLabel">Ajouter un Employé</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
            </div>
            <form action="{{ route('corps.personnel.store') }}" method="POST">
                <input type="hidden" name="form_type" value="create">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="create_prenom" class="form-label">Prénom *</label>
                            <input type="text" id="create_prenom" name="prenom" class="form-control" value="{{ old('prenom') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="create_nom" class="form-label">Nom *</label>
                            <input type="text" id="create_nom" name="nom" class="form-control" value="{{ old('nom') }}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="create_matricule" class="form-label">Matricule *</label>
                            <input type="text" id="create_matricule" name="matricule" class="form-control" value="{{ old('matricule') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="create_email" class="form-label">Email</label>
                            <input type="email" id="create_email" name="email" class="form-control" value="{{ old('email') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="create_soutes_ids" class="form-label">Soutes associées</label>
                        <select id="create_soutes_ids" name="soutes_ids[]" class="form-select soute-select" multiple>
                            @foreach($soutes as $soute)
                                <option value="{{ $soute->id }}" {{ (collect(old('soutes_ids'))->contains($soute->id)) ? 'selected' : '' }}>
                                    {{ $soute->nom }}
                                </option>
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

<div class="modal fade" id="editPersonnelModal" tabindex="-1" aria-labelledby="editPersonnelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPersonnelModalLabel">Modifier l'Employé</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="form_type" value="edit">
                <div class="modal-body">
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
    <div class="modal-dialog modal-sm"> 
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePersonnelModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer <strong id="personnelName"></strong> ?</p>
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>

</style>
@endpush

@push('custom-scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($errors->any() && old('form_type') === 'create')
            var createModalEl = document.getElementById('createPersonnelModal');
            if (createModalEl) {
                var createModal = new bootstrap.Modal(createModalEl);
                createModal.show();
            }
        @endif

        if ($('#create_soutes_ids').length) {
            $('#create_soutes_ids').select2({
                theme: "bootstrap-5",
                placeholder: "Sélectionnez des soutes",
                width: '100%',
                dropdownParent: $('#createPersonnelModal') 
            });
        }

        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const personnel = {
                    id: this.dataset.id,
                    nom: this.dataset.nom,
                    prenom: this.dataset.prenom,
                    matricule: this.dataset.matricule,
                    email: this.dataset.email || '',
                    soutes: JSON.parse(this.dataset.soutes || '[]')
                };

                const editForm = document.getElementById('editForm');
                editForm.action = `{{ url('corps/personnel') }}/${personnel.id}`;

                let optionsHtml = '';
                @foreach($soutes as $soute)
                    const selected = personnel.soutes.includes({{ $soute->id }}) ? 'selected' : '';
                    optionsHtml += `<option value="{{ $soute->id }}" ${selected}>{{ $soute->nom }}</option>`;
                @endforeach

                const modalBody = document.querySelector('#editPersonnelModal .modal-body');
                modalBody.innerHTML = `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_prenom" class="form-label">Prénom *</label>
                            <input type="text" id="edit_prenom" name="prenom" class="form-control" value="${personnel.prenom}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_nom" class="form-label">Nom *</label>
                            <input type="text" id="edit_nom" name="nom" class="form-control" value="${personnel.nom}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_matricule" class="form-label">Matricule *</label>
                            <input type="text" id="edit_matricule" name="matricule" class="form-control" value="${personnel.matricule}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" id="edit_email" name="email" class="form-control" value="${personnel.email}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_soutes_ids" class="form-label">Soutes associées</label>
                        <select id="edit_soutes_ids" name="soutes_ids[]" class="form-select soute-select-edit" multiple>
                            ${optionsHtml}
                        </select>
                    </div>
                `;

                if ($('#edit_soutes_ids').data('select2')) {
                    $('#edit_soutes_ids').select2('destroy'); 
                }
                $('#edit_soutes_ids').select2({
                    theme: "bootstrap-5",
                    placeholder: "Sélectionnez des soutes",
                    width: '100%',
                    dropdownParent: $('#editPersonnelModal') 
                });

                var editModal = new bootstrap.Modal(document.getElementById('editPersonnelModal'));
                editModal.show();
            });
        });

        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('personnelName').textContent = this.dataset.name;
                document.getElementById('deleteForm').action = `{{ url('corps/personnel') }}/${this.dataset.id}`; // URL dynamique
                var deleteModal = new bootstrap.Modal(document.getElementById('deletePersonnelModal'));
                deleteModal.show();
            });
        });
    });
</script>
@endpush
@extends('corpsArme.layouts.template')

@section('title', 'Liste du Personnel')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Liste du Personnel</h3>
                <p class="text-subtitle text-muted">Employés rattachés à votre corps d'armée.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('corps.gendarmerie.dashboard') }}">Tableau de Bord</a></li> {{-- Ajuster la route du dashboard --}}
                        <li class="breadcrumb-item active" aria-current="page">Personnel</li>
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
@if($errors->any() && old('form_type') === 'create_personnel')
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
                            <th>Service</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($personnels as $personnel)
                            <tr>
                                <td>{{ $personnel->matricule }}</td>
                                <td>{{ $personnel->nom_complet }}</td> {{-- Utilise l'accesseur --}}
                                <td>{{ $personnel->email ?? 'N/A' }}</td>
                                <td>{{ $personnel->service->nom ?? 'Aucun service' }}</td> {{-- Affiche le nom du service lié --}}
                                <td class="text-center">
                                    <a href="{{-- route('corps.personnel.edit', $personnel->id) --}}#" class="btn btn-sm btn-info" title="Modifier">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" title="Supprimer"
                                            onclick="confirmDeletePersonnel({{ $personnel->id }}, '{{ $personnel->nom_complet }}')">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                    <form id="delete-personnel-form-{{ $personnel->id }}" action="{{-- route('corps.personnel.destroy', $personnel->id) --}}#" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Aucun personnel trouvé pour ce corps d'armée.</td>
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

{{-- Modale de Création du Personnel --}}
<div class="modal fade" id="createPersonnelModal" tabindex="-1" aria-labelledby="createPersonnelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createPersonnelModalLabel">Ajouter un Nouvel Employé</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('corps.personnel.store') }}" method="POST">
                @csrf
                <input type="hidden" name="form_type" value="create_personnel">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('prenom') is-invalid @enderror" id="prenom" name="prenom" value="{{ old('prenom') }}" required>
                                @error('prenom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom') }}" required>
                                @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="matricule" class="form-label">Matricule <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('matricule') is-invalid @enderror" id="matricule" name="matricule" value="{{ old('matricule') }}" required>
                                @error('matricule') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="service_id" class="form-label">Service de rattachement</label>
                        <select class="form-select @error('service_id') is-invalid @enderror" id="service_id" name="service_id">
                            <option value="">-- Sélectionner un service (Optionnel) --</option>
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
                    <button type="submit" class="btn btn-primary">Enregistrer Employé</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDeletePersonnel(personnelId, personnelName) {
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Supprimer l'employé " + personnelName + " ? Cette action est irréversible !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-personnel-form-' + personnelId).submit();
                }
            })
        }

        @if($errors->any() && old('form_type') === 'create_personnel')
            document.addEventListener('DOMContentLoaded', function() {
                var createModal = new bootstrap.Modal(document.getElementById('createPersonnelModal'));
                createModal.show();
            });
        @endif
    </script>
@endsection
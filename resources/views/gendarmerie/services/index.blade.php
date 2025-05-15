@extends('gendarmerie.layouts.template')

@section('title', 'Liste des Services')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Liste des Services</h3>
                <p class="text-subtitle text-muted">Liste des services rattachés à votre corps d'armée.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('corps.dashboard') }}">Tableau de Bord</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Services</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    {{-- Message de succès --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <section class="section">
        <div class="card">
            <div class="card-header">
                {{-- Bouton pour ouvrir la modale --}}
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createServiceModal">
                    <i class="bi bi-plus-lg"></i> Ajouter un Service
                </button>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="table1">
                        <thead>
                            <tr>
                                <th>Nom du Service</th>
                                <th>Localisation</th>
                                <th>Date Création</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($services as $service)
                                <tr>
                                    <td>{{ $service->nom }}</td>
                                    <td>{{ $service->localisation }}</td>
                                    <td>{{ $service->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-center">
                                        {{-- Modifier --}}
                                        <a href="{{ route('corps.services.edit', $service->id) }}" class="btn btn-sm btn-info" title="Modifier">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        {{-- Supprimer --}}
                                        <button type="button" class="btn btn-sm btn-danger" title="Supprimer"
                                                onclick="confirmDelete({{ $service->id }})">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>

                                        {{-- Formulaire suppression --}}
                                        <form id="delete-form-{{ $service->id }}" action="{{ route('corps.services.destroy', $service->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Aucun service trouvé pour ce corps d'armée.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $services->links() }}
                </div>
            </div>
        </div>
    </section>

    {{-- Modale d'ajout --}}
    <div class="modal fade" id="createServiceModal" tabindex="-1" aria-labelledby="createServiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createServiceModalLabel">Ajouter un Nouveau Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('corps.services.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="form_type" value="create_service">
                    <div class="modal-body">
                        {{-- Nom du service --}}
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom du Service <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom') }}" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Localisation --}}
                        <div class="mb-3">
                            <label for="localisation" class="form-label">Localisation <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('localisation') is-invalid @enderror" id="localisation" name="localisation" value="{{ old('localisation') }}" required>
                            @error('localisation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer le Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(serviceId) {
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Vous ne pourrez pas annuler cette action !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + serviceId).submit();
                }
            });
        }

        // Afficher la modale automatiquement en cas d'erreurs de validation
        @if($errors->any())
            var createModal = new bootstrap.Modal(document.getElementById('createServiceModal'));
            createModal.show();
        @endif
    </script>
@endsection

@extends('corpsArme.layouts.template') {{-- Utilise ton layout principal --}}

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

    {{-- Affichage des messages de succès --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <section class="section">
        <div class="card">
            <div class="card-header">
                <a href="{{ route('corps.services.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Ajouter un Service
                </a>
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
                            {{-- Boucle sur les services passés par le contrôleur --}}
                            @forelse ($services as $service)
                                <tr>
                                    <td>{{ $service->nom }}</td>
                                    <td>{{ $service->localisation }}</td>
                                    <td>{{ $service->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-center">
                                        {{-- Bouton Modifier --}}
                                        <a href="{{ route('corps.services.edit', $service->id) }}" class="btn btn-sm btn-info" title="Modifier">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        {{-- Bouton Supprimer (avec confirmation) --}}
                                        <button type="button" class="btn btn-sm btn-danger" title="Supprimer"
                                                onclick="confirmDelete({{ $service->id }})">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                        {{-- Formulaire caché pour la suppression --}}
                                        <form id="delete-form-{{ $service->id }}" action="{{ route('corps.services.destroy', $service->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                {{-- Message si aucun service n'est trouvé --}}
                                <tr>
                                    <td colspan="4" class="text-center">Aucun service trouvé pour ce corps d'armée.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{-- Liens de pagination --}}
                <div class="mt-3">
                    {{ $services->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

{{-- Ajout de script pour la confirmation de suppression --}}
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
                    // Soumettre le formulaire de suppression correspondant
                    document.getElementById('delete-form-' + serviceId).submit();
                }
            })
        }
    </script>
@endsection
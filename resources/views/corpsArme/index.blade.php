@extends('admin.layouts.template')
@section('content') 

<!-- Insertion de SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
<div class="container">
    <!-- Notifications SweetAlert -->
    @if(Session::get('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Succès',
            text: '{{ Session::get('success') }}',
            showConfirmButton: true,
            confirmButtonText: 'OK',
            background: 'white',
            color: '#006600'
        });
    </script>
    @endif

    @if(Session::get('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: '{{ Session::get('error') }}',
            showConfirmButton: true,
            confirmButtonText: 'OK',
            background: '#ffcccc',
            color: '#b30000'
        });
    </script>
    @endif
 <!-- Tableau des administrateurs -->
 <div class="row mt-5">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-black">
                <h4 class="font-weight-bolder text-black mb-0 text-center">Liste des corps d'armées</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr class="text-center" style="background-color: black;">
                                <th class="text-white align-middle">Corps d'armée</th>
                                <th class="text-white align-middle">Email</th>
                                <th class="text-white align-middle">Localisation</th>
                                <th class="text-white align-middle">Date d'inscription</th>
                                <th class="text-white align-middle">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($corpsArmes as $corpsArme)
                                <tr class="text-center">
                                    <td class="align-middle">{{ $corpsArme->name }}</td>
                                    <td class="align-middle">{{ $corpsArme->email }}</td>
                                    <td class="align-middle">{{ $corpsArme->localisation }}</td>
                                    <td class="align-middle">{{ $corpsArme->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="align-middle">
                                        <a href="{{ route('admin.edit.army', $corpsArme->id) }}" class="btn btn-sm btn-primary edit-btn" title="Modifier">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                        <form id="delete-form-{{ $corpsArme->id }}" action="{{ route('admin.delete.army', $corpsArme->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <a href="#" class="btn btn-sm btn-danger delete-btn" title="Supprimer" onclick="confirmDelete({{ $corpsArme->id }})">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">Aucun administrateur trouvé</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script>
     // Fonction de confirmation de suppression
     function confirmDelete(adminId) {
            event.preventDefault();
            
            Swal.fire({
                title: 'Êtes-vous sûr?',
                text: "Vous ne pourrez pas annuler cette action!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer!',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Soumettre le formulaire de suppression
                    document.getElementById('delete-form-' + adminId).submit();
                }
            });
        }
    </script>
</script>
@endsection 
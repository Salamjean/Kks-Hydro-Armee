@extends('superadmin.layouts.template')

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

        <!-- Formulaire de création -->
        <div class="row justify-content-center align-items-center">
            <div class="col-xl-5 col-lg-6 col-md-8">
                <div class="card card-plain" style="box-shadow: 0 20px 35px 0 rgba(0, 0, 0, 0.1), 0 15px 25px -5px rgba(0, 0, 0, 0.04); border-radius: 10px;">
                    <div class="card-header text-center bg-transparent">
                        <h4 class="font-weight-bolder">Inscription Administrateur de SEA</h4>
                        <p class="mb-0">Remplissez les informations pour créer un nouveau compte administrateur</p>
                    </div>
                    <div class="card-body px-5 pt-0">
                        <form role="form" method="POST" action="{{ route('superadmin.store.SEA') }}">
                            @csrf
                            @method('POST')
                            
                            <!-- Champ Nom -->
                            <div class="input-group input-group-outline mb-3 mt-3">
                                <label class="form-label">Nom complet</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" >
                            </div>
                            @error('name')
                                <div class="text-danger text-center">{{ $message }}</div>
                            @enderror
                            
                            <!-- Champ Matricule -->
                            <div class="input-group input-group-outline mb-3">
                                <label class="form-label">Matricule</label>
                                <input type="text" class="form-control" name="matricule" value="{{ old('matricule') }}" >
                            </div>
                            @error('matricule')
                                <div class="text-danger text-center">{{ $message }}</div>
                            @enderror
                            
                            <!-- Champ Email -->
                            <div class="input-group input-group-outline mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}" >
                            </div>
                            @error('email')
                                <div class="text-danger text-center">{{ $message }}</div>
                            @enderror
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-lg bg-gradient-dark btn-lg w-100 mt-4 mb-2" style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                                    Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des administrateurs -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-black">
                        <h4 class="font-weight-bolder text-black mb-0 text-center">Liste des Administrateurs</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr class="text-center" style="background-color: black;">
                                        <th class="text-white align-middle">Nom complet</th>
                                        <th class="text-white align-middle">Matricule</th>
                                        <th class="text-white align-middle">Email</th>
                                        <th class="text-white align-middle">Date de création</th>
                                        <th class="text-white align-middle">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($admins as $admin)
                                        <tr class="text-center">
                                            <td class="align-middle">{{ $admin->name }}</td>
                                            <td class="align-middle">{{ $admin->matricule }}</td>
                                            <td class="align-middle">{{ $admin->email }}</td>
                                            <td class="align-middle">{{ $admin->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="align-middle">
                                                <a href="{{ route('superadmin.edit.SEA', $admin->id) }}" class="btn btn-sm btn-primary edit-btn" title="Modifier">
                                                    <i class="fas fa-edit"></i> Modifier
                                                </a>
                                                <form id="delete-form-{{ $admin->id }}" action="{{ route('superadmin.delete.SEA', $admin->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                                <a href="#" class="btn btn-sm btn-danger delete-btn" title="Supprimer" onclick="confirmDelete({{ $admin->id }})">
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
        // Fonction pour le mot de passe
        function togglePassword() {
            const passwordFields = document.querySelectorAll('input[type="password"]');
            const showPassword = document.getElementById('showPassword').checked;
            
            passwordFields.forEach(field => {
                field.type = showPassword ? 'text' : 'password';
            });
        }

        // Fonction de confirmation de suppression
        function confirmDelete(adminId) {
            event.preventDefault(); // Empêche le comportement par défaut du lien

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
                    // Soumettez le formulaire de suppression
                    document.getElementById('delete-form-' + adminId).submit();
                }
            });
        }
    </script>
@endsection
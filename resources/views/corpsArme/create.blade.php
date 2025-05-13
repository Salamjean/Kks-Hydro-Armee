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

        <div class="row justify-content-center">
            <!-- Formulaire à gauche -->
            <div class="col-md-6">
                <div class="card card-plain mt-4" style="box-shadow: 0 20px 35px 0 rgba(0, 0, 0, 0.1), 0 15px 25px -5px rgba(0, 0, 0, 0.04); border-radius: 10px;">
                    <div class="card-header text-center bg-transparent">
                        <h4 class="font-weight-bolder">Inscription un corps</h4>
                        <p class="mb-0">Remplissez les informations pour créer un nouveau du corps </p>
                    </div>
                    <div class="card-body px-5 pt-0">
                        <form role="form" method="POST" action="{{ route('admin.store.army') }}">
                            @csrf
                            @method('POST')
                            
                            <!-- Champ Nom -->
                            <div class="input-group input-group-outline mb-3 mt-3">
                                <select class="form-control text-center" name="name">
                                    <option value="">Sélectionnez le corps d'armée</option>
                                    <option value="Gendarmerie" {{ old('name') == 'Gendarmerie' ? 'selected' : '' }}>Gendarmerie</option>
                                    <option value="Marine" {{ old('name') == 'Marine' ? 'selected' : '' }}>Marine</option>
                                    <option value="Armée-Air" {{ old('name') == 'Armée-Air' ? 'selected' : '' }}>Armée de l'air</option>
                                    <option value="Armée-Terre" {{ old('name') == 'Armée-Terre' ? 'selected' : '' }}>Armée de Terre</option>
                                </select>
                            </div>
                            @error('name')
                                <div class="text-danger text-center">{{ $message }}</div>
                            @enderror
                            
                            <!-- Champ localisation -->
                            <div class="mb-3 text-center">
                                <label class="form-label">Localisation</label>
                                <div class="input-group input-group-outline justify-content-center">
                                    <input type="text" class="form-control text-center" name="localisation" value="{{ old('localisation') }}">
                                </div>
                            </div>
                            @error('localisation')
                                <div class="text-danger text-center">{{ $message }}</div>
                            @enderror
                            
                            <!-- Champ Email -->
                            <div class="text-center">
                                <label class="form-label">Email</label>
                                <div class="input-group input-group-outline mb-3 justify-content-center">
                                    <input type="email" class="form-control text-center" name="email" value="{{ old('email') }}">
                                </div>
                            </div>
                            @error('email')
                                <div class="text-danger text-center">{{ $message }}</div>
                            @enderror
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-lg bg-gradient-dark btn-lg w-100 mt-4 mb-2" style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); background-color:#435ebe; color:white;">
                                    Enregistrer
                                </button>
                            </div>
                        </form>
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
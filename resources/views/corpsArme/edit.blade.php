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

        <!-- Formulaire de modification -->
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="col-xl-5 col-lg-6 col-md-8">
                <div class="card card-plain" style="box-shadow: 0 20px 35px 0 rgba(0, 0, 0, 0.1), 0 15px 25px -5px rgba(0, 0, 0, 0.04); border-radius: 10px;">
                    <div class="card-header text-center bg-transparent">
                        <h4 class="font-weight-bolder">Modification d'un corps</h4>
                        <p class="mb-0">Mettez à jour les informations du corps</p>
                    </div>
                    <div class="card-body px-5 pt-0">
                        <form role="form" method="POST" action="{{ route('admin.update.army', $corpsArme->id) }}">
                            @csrf
                            @method('PUT') <!-- Utilisation de PUT pour la mise à jour -->
                            
                            <!-- Champ Nom -->
                            <div class="input-group input-group-outline mb-3 mt-3">
                                <select class="form-control" name="name">
                                    <option value="">Sélectionnez le corps d'armée</option>
                                    <option value="Gendarmerie" {{ $corpsArme->name == 'Gendarmerie' ? 'selected' : '' }}>Gendarmerie</option>
                                    <option value="Marine" {{ $corpsArme->name == 'Marine' ? 'selected' : '' }}>Marine</option>
                                    <option value="Armée-Air" {{ $corpsArme->name == 'Armée-Air' ? 'selected' : '' }}>Armée de l'air</option>
                                    <option value="Armée-Terre" {{ $corpsArme->name == 'Armée-Terre' ? 'selected' : '' }}>Armée de Terre</option>
                                </select>
                            </div>
                            
                            <!-- Champ Localisation -->
                            <div class="input-group input-group-outline mb-3">
                                <label class="form-label">Localisation</label>
                                <input type="text" class="form-control" name="localisation" value="{{ old('localisation', $corpsArme->localisation) }}">
                            </div>
                            @error('localisation')
                                <div class="text-danger text-center">{{ $message }}</div>
                            @enderror
                            
                            <!-- Champ Email -->
                            <div class="input-group input-group-outline mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email', $corpsArme->email) }}">
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
    </div>

    <script>
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
@extends('armee-terre.layouts.template')

@section('title', 'Modifier le Profil de l\'Armée de terre')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow rounded-4 p-4 w-100" style="max-width: 600px;">

        {{-- Messages de succès --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
        @endif

        {{-- Messages d'erreur --}}
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Erreurs de validation :</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
        @endif

        <h3 class="text-center mb-3 text-primary">Modifier votre profil</h3>
        <p class="text-center text-muted mb-4">Mettez à jour vos informations personnelles.</p>

        <form method="POST" action="">
            @csrf

            <div class="row">
                {{-- Nom --}}
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" id="nom" name="nom" value="{{ old('nom') }}"
                            class="form-control @error('nom') is-invalid @enderror"
                            placeholder="ex: Dupont" required>
                        @error('nom')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Prénom --}}
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" id="prenom" name="prenom" value="{{ old('prenom') }}"
                            class="form-control @error('prenom') is-invalid @enderror"
                            placeholder="ex: Jean" required>
                        @error('prenom')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Email --}}
            <div class="form-group mb-3">
                <label for="email" class="form-label">Adresse Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="ex: armee@exemple.com" required>
                @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Mot de passe --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" id="password" name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Nouveau mot de passe" required>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Confirmation du mot de passe --}}
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="form-control @error('password_confirmation') is-invalid @enderror"
                            placeholder="Confirmez le mot de passe" required>
                        @error('password_confirmation')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Bouton --}}
            <button type="submit" class="btn btn-primary w-100">Mettre à jour le profil</button>
        </form>
    </div>
</div>
@endsection

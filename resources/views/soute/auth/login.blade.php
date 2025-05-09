{{-- Adapte un de tes formulaires de login existants (ex: corpsArme/auth/login.blade.php) --}}
{{-- Il devra contenir les champs : --}}
{{-- 1. Email ou Matricule Employé (name="email_or_matricule") --}}
{{-- 2. Matricule de la Soute (name="matricule_soute") --}}
{{-- 3. Mot de passe (name="password") - Ce champ peut être affiché/caché via JS si c'est la première connexion,
       ou simplement le contrôleur le gérera s'il est vide. Pour simplifier, on le montre toujours. --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Soute</title>
    <link rel="stylesheet" href="{{ asset('assetsSEA/css/bootstrap.css') }}"> {{-- Adapte le chemin --}}
    <link rel="stylesheet" href="{{ asset('assetsSEA/css/pages/auth.css') }}">
</head>
<body>
    <div id="auth">
        <div class="row h-100">
            <div class="col-lg-5 col-12">
                <div id="auth-left">
                    <h1 class="auth-title">Connexion Espace Soute</h1>
                    <p class="auth-subtitle mb-5">Connectez-vous avec vos identifiants.</p>

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('soute.dashboard.handleLogin') }}">
                        @csrf
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="text" class="form-control form-control-xl @error('email_or_matricule') is-invalid @enderror"
                                   name="email_or_matricule" value="{{ old('email_or_matricule') }}" placeholder="Votre Email ou Matricule Employé" required>
                            <div class="form-control-icon"><i class="bi bi-person"></i></div>
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="text" class="form-control form-control-xl @error('matricule_soute') is-invalid @enderror"
                                   name="matricule_soute" value="{{ old('matricule_soute') }}" placeholder="Matricule de la Soute" required>
                            <div class="form-control-icon"><i class="bi bi-hdd-stack"></i></div>
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" class="form-control form-control-xl @error('password') is-invalid @enderror"
                                   name="password" placeholder="Mot de passe (laisser vide si 1ère connexion)">
                            <div class="form-control-icon"><i class="bi bi-shield-lock"></i></div>
                        </div>
                        <button class="btn btn-primary btn-block btn-lg shadow-lg mt-5">Se connecter</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-7 d-none d-lg-block">
                <div id="auth-right"></div>
            </div>
        </div>
    </div>
</body>
</html>
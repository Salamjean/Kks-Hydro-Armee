{{-- Adapte un de tes formulaires de définition de mot de passe (ex: corpsArme/auth/validate.blade.php) --}}
{{-- Il devra contenir les champs : --}}
{{-- 1. Nouveau Mot de passe (name="password") --}}
{{-- 2. Confirmation Mot de passe (name="password_confirmation") --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Définir votre Mot de Passe</title>
    <link rel="stylesheet" href="{{ asset('assetsSEA/css/bootstrap.css') }}"> {{-- Adapte le chemin --}}
    <link rel="stylesheet" href="{{ asset('assetsSEA/css/pages/auth.css') }}">
</head>
<body>
    <div id="auth">
        <div class="row h-100">
            <div class="col-lg-5 col-12">
                <div id="auth-left">
                    <h1 class="auth-title">Définir votre Mot de Passe</h1>
                    <p class="auth-subtitle mb-5">Ceci est votre première connexion. Veuillez définir un mot de passe sécurisé.</p>

                    @if (session('status'))
                        <div class="alert alert-info" role="alert">
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

                    <form method="POST" action="{{ route('soute.dashboard.handleSet.password') }}">
                        @csrf
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" class="form-control form-control-xl @error('password') is-invalid @enderror"
                                   name="password" placeholder="Nouveau mot de passe" required>
                            <div class="form-control-icon"><i class="bi bi-shield-lock"></i></div>
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" class="form-control form-control-xl"
                                   name="password_confirmation" placeholder="Confirmer le mot de passe" required>
                            <div class="form-control-icon"><i class="bi bi-shield-check"></i></div>
                        </div>
                        <button class="btn btn-primary btn-block btn-lg shadow-lg mt-5">Définir et se connecter</button>
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
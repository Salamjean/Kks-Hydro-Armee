<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Corps d'Armée - {{ config('app.name', 'Laravel') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assetsSEA/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assetsSEA/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assetsSEA/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assetsSEA/css/pages/auth.css') }}">
</head>

<body>
    <div id="auth">

        <div class="row h-100">
            <div class="col-lg-5 col-12">
                <div id="auth-left">
                    {{-- <div class="auth-logo">
                        <a href="#"><img src="{{ asset('assets/images/logo/logo.png') }}" alt="Logo"></a>
                    </div> --}}
                    <h1 class="auth-title">Connexion Corps d'Armée</h1>
                    <p class="auth-subtitle mb-5">Connectez-vous avec votre email et votre mot de passe.</p>

                    {{-- Affichage des messages de succès (ex: après définition du mdp) --}}
                    @if (Session::has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ Session::get('success') }}
                             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                     {{-- Affichage des messages d'erreur généraux (ex: identifiants incorrects) --}}
                     {{-- L'erreur 'email' est souvent utilisée pour les identifiants incorrects --}}
                     @if ($errors->has('email') && !$errors->has('password'))
                         <div class="alert alert-danger alert-dismissible fade show" role="alert">
                             {{ $errors->first('email') }} {{-- Affiche le message d'erreur lié à l'email/identifiants --}}
                             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                         </div>
                     @endif
                     {{-- Affichage d'autres erreurs spécifiques si nécessaire --}}
                     @if (Session::has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ Session::get('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif


                    {{-- Formulaire de connexion --}}
                    <form method="POST" action="{{ route('corps.handle.login') }}">
                        @csrf

                        {{-- Champ Email --}}
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="email" class="form-control form-control-xl @error('email') is-invalid @enderror"
                                   placeholder="Adresse Email" name="email" value="{{ old('email') }}" required autofocus>
                            <div class="form-control-icon">
                                <i class="bi bi-envelope"></i>
                            </div>
                             {{-- Affiche l'erreur spécifique à l'email (format, obligatoire) SEULEMENT s'il n'y a pas d'erreur sur le mot de passe aussi --}}
                            @error('email')
                                @if (!$errors->has('password'))
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @endif
                            @enderror
                        </div>

                        {{-- Champ Mot de Passe --}}
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" class="form-control form-control-xl @error('password') is-invalid @enderror"
                                   placeholder="Mot de passe" name="password" required>
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                             @error('password')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Checkbox "Se souvenir de moi" --}}
                        <div class="form-check form-check-lg d-flex align-items-end">
                            <input class="form-check-input me-2" type="checkbox" name="remember" id="flexCheckDefault" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label text-gray-600" for="flexCheckDefault">
                                Se souvenir de moi
                            </label>
                        </div>

                        {{-- Bouton de soumission --}}
                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-5">Se connecter</button>
                    </form>

                    {{-- Liens optionnels --}}
                    <div class="text-center mt-5 text-lg fs-4">
                        {{-- Si vous implémentez la réinitialisation de mot de passe pour les corps plus tard : --}}
                        {{-- <p><a class="font-bold" href="{{ route('corps.password.request') }}">Mot de passe oublié ?</a></p> --}}
                         {{-- Le lien d'inscription n'est pas pertinent ici car créé par l'admin --}}
                        {{-- <p class="text-gray-600">Pas encore de compte? <a href="#" class="font-bold">S'inscrire</a>.</p> --}}
                    </div>
                </div>
            </div>
            <div class="col-lg-7 d-none d-lg-block">
                <div id="auth-right" style="display: flex; justify-content: center; align-items: center; background: #f2f7ff;">
                     {{-- Vous pouvez mettre une image pertinente ici --}}
                     <img src="{{ asset('assetsSEA/images/samples/login-vector.jpg') }}" alt="Illustration Connexion" style="max-width: 80%; max-height: 80%; object-fit: contain;">
                 </div>
            </div>
        </div>

    </div>
    <script src="{{ asset('assetsSEA/js/bootstrap.bundle.min.js') }}"></script> {{-- Ajout pour les alertes dismissible --}}
</body>

</html>
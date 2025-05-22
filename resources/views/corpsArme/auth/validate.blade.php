<!DOCTYPE html>
<html lang="fr">

<head>
   <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assetsSEA/css/bootstrap.css') }}">
  <link rel="stylesheet" href="{{ asset('assetsSEA/vendors/iconly/bold.css') }}">
  {{-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> --}}
  <link rel="stylesheet" href="{{ asset('assetsSEA/vendors/perfect-scrollbar/perfect-scrollbar.css') }}">
  <link rel="stylesheet" href="{{ asset('assetsSEA/vendors/bootstrap-icons/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assetsSEA/css/app.css') }}">
  {{-- <link rel="shortcut icon" href="{{ asset('assetsSEA/images/favicon.svg" type="image/x-icon') }}"> --}}
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>
    <div id="auth">
        <div class="row h-100">
            <div class="col-lg-5 col-12">
                <div id="auth-left">
                    {{-- <div class="auth-logo">
                        <a href="#"><img src="{{ asset('assets/images/logo/logo.png') }}" alt="Logo"></a>
                    </div> --}}
                    <h1 class="auth-title">Finaliser votre inscription</h1>
                    <p class="auth-subtitle mb-4">Veuillez entrer le code reçu par email et définir votre mot de passe.</p>

                    {{-- Affichage des erreurs de validation --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                     {{-- Affichage des messages d'erreur généraux --}}
                     @if(Session::has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ Session::get('error') }}
                             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Formulaire de définition d'accès --}}
                    <form method="POST" action="{{ route('corps.submit.define.access') }}">
                        @csrf
                        {{-- Champ caché pour l'email --}}
                        <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

                        {{-- Champ Code --}}
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="text" class="form-control form-control-xl @error('code') is-invalid @enderror"
                                   placeholder="Code reçu par email" name="code" value="{{ old('code') }}" required>
                            <div class="form-control-icon">
                                <i class="bi bi-patch-check"></i>
                            </div>
                            @error('code')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Champ Nouveau Mot de Passe --}}
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" class="form-control form-control-xl @error('password') is-invalid @enderror"
                                   placeholder="Nouveau mot de passe" name="password" required>
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Champ Confirmer Mot de Passe --}}
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" class="form-control form-control-xl @error('confirme_password') is-invalid @enderror"
                                   placeholder="Confirmer le mot de passe" name="confirme_password" required>
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock-fill"></i>
                            </div>
                             @error('confirme_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <button class="btn btn-primary btn-block btn-lg shadow-lg mt-5">Valider et définir le mot de passe</button>
                    </form>

                </div>
            </div>
            <div class="col-lg-7 d-none d-lg-block">
                <div id="auth-right" style="display: flex; justify-content: center; align-items: center; background: #f2f7ff;">
                     {{-- Vous pouvez mettre une image pertinente ici --}}
                     <img src="{{ asset('path/to/your/image.jpg') }}" alt="Illustration" style="max-width: 80%; max-height: 80%; object-fit: contain;">
                 </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assetsSEA/js/bootstrap.bundle.min.js') }}"></script> {{-- Ajout pour les alertes dismissible --}}
</body>
</html>
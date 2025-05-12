<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/assets/img/apple-icon.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('assets/assets/img/favicon.png') }}">
  <title>CONNEXION - SEA</title>
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <link href="{{ asset('assets/assets/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link id="pagestyle" href="{{ asset('assets/assets/css/material-dashboard.css?v=3.2.0') }}" rel="stylesheet" />
  {{-- Placez vos styles personnalisés ici ou dans un fichier séparé chargé après material-dashboard.css --}}
  <style>
    body.login-page { /* Ajoutez cette classe au body si vous voulez des styles spécifiques */
        /* background-color: #f8f9fa; -- Si vous n'utilisez pas d'image de fond */
    }

    /* Wrapper principal du contenu d'authentification */
    .auth-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh; /* Prend toute la hauteur de la vue */
        padding: 20px;
        background-size: cover;
        background-position: center;
    }

    /* Carte pour le formulaire */
    .auth-card {
        background-color: rgba(255, 255, 255, 0.95); /* Fond blanc légèrement transparent */
        border-radius: 0.75rem; /* Bords arrondis Material */
        box-shadow: 0 4px 20px 0 rgba(0,0,0,0.1), 0 7px 10px -5px rgba(0,0,0,0.05); /* Ombre Material */
        padding: 2rem 2.5rem; /* Espacement interne */
        width: 100%;
        max-width: 450px; /* Largeur maximale du formulaire */
        margin: auto; /* Centrage */
    }

    .auth-card .auth-logo {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .auth-card .auth-logo img {
        max-width: 100px; /* Ajustez la taille de votre logo */
        height: auto;
    }

    .auth-card .auth-title {
        font-size: 1.5rem; /* Taille du titre "Connexion" */
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #344767; /* Couleur de texte Material */
        text-align: center;
    }

    .auth-card .auth-subtitle {
        font-size: 0.9rem;
        color: #6c757d; /* Couleur de sous-titre plus discrète */
        margin-bottom: 2rem;
        text-align: center;
    }

    /* Champs de formulaire - Material Dashboard gère bien input-group-outline */
    .auth-card .input-group.input-group-outline {
        margin-bottom: 1.5rem !important; /* Espacement un peu plus grand */
    }

    /* Messages d'erreur sous les champs */
    .auth-card .text-danger.field-error {
        font-size: 0.8rem;
        display: block; /* Pour qu'il prenne sa propre ligne */
        margin-top: -1rem; /* Pour le rapprocher du champ */
        margin-bottom: 1rem;
        text-align: left; /* Alignement à gauche pour les erreurs de champ */
    }

    /* Alerte d'erreur générale */
    .auth-card .alert-danger {
        border-radius: 0.5rem;
        padding: 0.8rem 1rem;
        font-size: 0.9rem;
    }
    .auth-card .alert-danger ul {
        margin-bottom: 0;
        padding-left: 1.2rem;
    }

    .auth-card .btn-primary.submit-btn {
        background-image: linear-gradient(195deg, #0083ee 0%, #0083ee 100%); /* Gradient Material */
        border: none;
        padding: 0.75rem 1.5rem;
        font-size: 0.9rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        width: 100%; 
        margin-top: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 3px 3px 0 rgba(233, 30, 99, 0.15), 0 3px 1px -2px rgba(233, 30, 99, 0.2), 0 1px 5px 0 rgba(233, 30, 99, 0.15);
    }
    .auth-card .btn-primary.submit-btn:hover {
        background-image: linear-gradient(195deg, #085002 0%, #03520d 100%);
        box-shadow: 0 4px 7px -1px rgba(0,0,0,.11),0 2px 4px -1px rgba(0,0,0,.07);
    }


    /* Options 'Se souvenir' et 'Mot de passe oublié' */
    .auth-card .form-group.extra-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
        margin-bottom: 1.5rem;
    }
    .auth-card .form-check-label,
    .auth-card .forgot-password-link {
        color: #6c757d;
    }
    .auth-card .forgot-password-link:hover {
        color: #D81B60; /* Couleur Material au survol */
        text-decoration: underline;
    }

    /* Bouton Google Login */
    .auth-card .btn-google-login {
        background-color: #fff;
        color: #495057;
        border: 1px solid #ced4da;
        padding: 0.6rem 1.5rem;
        font-size: 0.9rem;
        width: 100%;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);
        transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out;
    }
    .auth-card .btn-google-login img {
        margin-right: 0.75rem;
        width: 20px; /* Taille de l'icône Google */
    }
    .auth-card .btn-google-login:hover {
        background-color: #f8f9fa;
        border-color: #adb5bd;
    }

    /* Lien 'S'inscrire' */
    .auth-card .signup-link-wrapper {
        text-align: center;
        font-size: 0.9rem;
        margin-top: 1.5rem;
        color: #6c757d;
    }
    .auth-card .signup-link-wrapper .signup-link {
        color: #034d0f; /* Couleur Material */
        font-weight: 500;
    }
    .auth-card .signup-link-wrapper .signup-link:hover {
        text-decoration: underline;
    }

    /* Liens du footer de la page d'authentification */
    .auth-footer-links {
        text-align: center;
        margin-top: 2rem;
        font-size: 0.85rem;
    }
    .auth-footer-links li {
        display: inline-block;
        margin: 0 10px;
    }
    .auth-footer-links li a {
        color: rgba(255, 255, 255, 0.7); 
        text-decoration: none;
    }
    .auth-footer-links li a:hover {
        color: #fff;
        text-decoration: underline;
    }

    .auth-copyright {
        text-align: center;
        margin-top: 0.5rem;
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.6);
    }
  </style>
</head>

<body class="login-page"> 
 
  <main class="main-content mt-0">
    <div class="auth-wrapper" style="background-image: url({{ url('assets/images/auth/login_1.jpg') }});">
      <div class="auth-card">
        {{-- <div class="auth-logo">
          <img src="{{ asset('assets/assets/img/favicon.png') }}" alt="Logo SEA">
        </div> --}}
        <h4 class="auth-title">Bienvenue !</h4>
        <p class="auth-subtitle">Connectez-vous pour continuer.</p>
        <form role="form" method="POST" action="{{ route('admin.handleLogin') }}">
          @csrf
          @if (session('error')) 
            <div class="alert alert-danger text-white" role="alert">
                {{ session('error') }}
            </div>
          @endif
          @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
            <div class="alert alert-danger text-white">
              <ul>
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif
          <div class="input-group input-group-outline my-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" value="{{ old('email') }}" name="email" required>
          </div>
          @error('email')
            <div class="text-danger field-error">{{ $message }}</div>
          @enderror
          <div class="input-group input-group-outline my-3">
            <label class="form-label">Mot de passe</label>
            <input type="password" class="form-control" name="password" required>
          </div>
          @error('password')
            <div class="text-danger field-error">{{ $message }}</div>
          @enderror
          <div class="form-group extra-options">
            <div class="form-check form-switch d-flex align-items-center mb-3">
              {{-- <input class="form-check-input" type="checkbox" id="rememberMe" name="remember" checked> --}}
              {{-- <label class="form-check-label mb-0 ms-3" for="rememberMe">Se souvenir de moi</label> --}}
            </div>
            <a href="#" class="forgot-password-link">Mot de passe oublié ?</a>
          </div>

          <div class="text-center">
            <button type="submit" class="btn btn-primary submit-btn w-100 my-4 mb-2">Connexion</button>
          </div>

          {{-- <div class="text-center">
            <button type="button" class="btn btn-google-login w-100 mb-3">
              <img src="{{ url('assets/images/file-icons/icon-google.svg') }}" alt="">
              Se connecter avec Google
            </button>
          </div> --}}

          {{-- <div class="signup-link-wrapper mt-4 text-center">
            Vous n'avez pas de compte ?
            <a href="#" class="text-primary text-gradient font-weight-bold signup-link">S'inscrire</a>
          </div> --}}
        </form>
      </div>
    </div>
  </main>

  <script src="{{ asset('assets/assets/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('assets/assets/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('assets/assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = { damping: '0.5' }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script src="{{ asset('assets/assets/js/material-dashboard.min.js?v=3.2.0') }}"></script>
</body>
</html>
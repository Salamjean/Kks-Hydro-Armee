<!DOCTYPE html>
<html lang="en">

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
  <link rel="shortcut icon" href="{{ asset('assetsSEA/images/favicon.svg" type="image/x-icon') }}">
  <style>
    body.login-page {
      /* background-color: #f8f9fa; */
    }

    .auth-wrapper {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 20px;
      background-size: cover;
      background-position: center;
    }

    .auth-card {
      background-color: rgba(255, 255, 255, 0.95);
      border-radius: 0.75rem;
      box-shadow: 0 4px 20px 0 rgba(0,0,0,0.1), 0 7px 10px -5px rgba(0,0,0,0.05);
      padding: 2rem 2.5rem;
      width: 100%;
      max-width: 450px;
      margin: auto;
    }

    .auth-card .auth-logo {
      text-align: center;
      margin-bottom: 1.5rem;
    }

    .auth-card .auth-logo img {
      max-width: 100px;
      height: auto;
    }

    .auth-card .auth-title {
      font-size: 1.5rem;
      font-weight: 500;
      margin-bottom: 0.5rem;
      color: #344767;
      text-align: center;
    }

    .auth-card .auth-subtitle {
      font-size: 0.9rem;
      color: #6c757d;
      margin-bottom: 2rem;
      text-align: center;
    }

    /* Styles pour les champs de formulaire - Material Dashboard gère bien input-group-outline */
    .auth-card .input-group.input-group-outline {
      margin-bottom: 1rem; /* Espacement standard */
    }
    /* Ajustement pour l'espace quand il y a une erreur en dessous */
    .auth-card .input-group.input-group-outline + .field-error {
        margin-top: -0.75rem; /* Rapprocher l'erreur du champ */
        margin-bottom: 1rem; /* Garder un espace avant le champ suivant */
    }
    /* S'assurer que le label est correctement positionné par Material Dashboard */
    .auth-card .input-group.input-group-outline .form-label {
        /* Material Dashboard gère cela, mais on peut forcer si besoin */
        /* Exemple : top: 0.7rem; si l'animation ne se fait pas bien */
    }

    /* Styles pour les labels des champs (non-flottants si jamais utilisés) */
    .auth-card .form-label-static { /* Si vous voulez un label fixe au-dessus */
        font-size: .875rem;
        font-weight: 500;
        color: #344767;
        margin-bottom: 0.5rem;
        display: block;
    }


    .auth-card .text-danger.field-error {
      font-size: 0.8rem;
      display: block;
      /* margin-top: -1rem; -- Géré par la règle plus haut */
      /* margin-bottom: 1rem; -- Géré par la règle plus haut */
      text-align: left;
    }

    .auth-card .alert-danger {
      border-radius: 0.5rem;
      padding: 0.8rem 1rem;
      font-size: 0.9rem;
      margin-bottom: 1.5rem; /* Espace sous l'alerte */
    }
    .auth-card .alert-danger ul {
      margin-bottom: 0;
      padding-left: 1.2rem;
    }

    .auth-card .btn-primary.submit-btn {
      background-image: linear-gradient(195deg, #0083ee 0%, #0083ee 100%);
      border: none;
      padding: 0.75rem 1.5rem;
      font-size: 0.9rem;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      width: 100%;
      margin-top: 1.5rem; /* Espace au-dessus du bouton */
      margin-bottom: 1rem;
      box-shadow: 0 3px 3px 0 rgba(233, 30, 99, 0.15), 0 3px 1px -2px rgba(233, 30, 99, 0.2), 0 1px 5px 0 rgba(233, 30, 99, 0.15);
    }
    .auth-card .btn-primary.submit-btn:hover {
      background-image: linear-gradient(195deg, #085002 0%, #03520d 100%);
      box-shadow: 0 4px 7px -1px rgba(0,0,0,.11),0 2px 4px -1px rgba(0,0,0,.07);
    }

    .auth-card .form-group.extra-options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 0.85rem;
      margin-bottom: 1.5rem; /* Espace sous les options */
      margin-top: 0.5rem; /* Espace au-dessus des options, après les erreurs potentielles */
    }
    .auth-card .form-check-label,
    .auth-card .forgot-password-link {
      color: #6c757d;
    }
    .auth-card .form-check .form-check-input {
      margin-top: 0.1em; /* Petit ajustement vertical pour le switch */
    }
    .auth-card .forgot-password-link:hover {
      color: #D81B60;
      text-decoration: underline;
    }

    .auth-card .signup-link-wrapper {
      text-align: center;
      font-size: 0.9rem;
      margin-top: 1.5rem;
      color: #6c757d;
    }
    .auth-card .signup-link-wrapper .signup-link {
      color: #034d0f;
      font-weight: 500;
    }
    .auth-card .signup-link-wrapper .signup-link:hover {
      text-decoration: underline;
    }

  </style>
</head>

<body class="login-page">

  <main class="main-content mt-0">
    <div class="auth-wrapper" style="background-image: url({{ url('assets/images/auth/login_1.jpg') }});">
      <div class="auth-card">
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
          {{-- Champ Email --}}
          <div class="form-group mb-4">
            <label for="email" class="custom-label">Email :</label>
            <input
              type="email"
              class="form-control custom-input @error('email') is-invalid @enderror"
              id="email"
              name="email"
              value="{{ old('email') }}"
              placeholder="ex: exemple@mail.com"
              required
            >
            @error('email')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>
          {{-- Champ Email --}}
          <div class="form-group mb-4">
            <label class="form-label" for="password">Mot de passe :</label>
            <input
              type="password"
              class="form-control custom-input @error('password') is-invalid @enderror"
              id="password"
              name="password"
              value="{{ old('password') }}"
              placeholder="***********"
              required
            >
            @error('email')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>
          <div class="form-group extra-options">
            <div class="form-check form-switch d-flex align-items-center">
              <input class="form-check-input" type="checkbox" id="rememberMe" name="remember" {{ old('remember') ? 'checked' : (request()->isMethod('get') ? 'checked' : '') }}>
              <label class="form-check-label mb-0 ms-2" for="rememberMe">Se souvenir de moi</label>
            </div>
            <a href="#" class="forgot-password-link text-sm">Mot de passe oublié ?</a>
          </div>

          <div class="text-center">
            <button type="submit" class="btn btn-primary submit-btn w-100">Connexion</button>
          </div>
        </form>

        <form role="form" method="POST" action="{{ route('admin.validate', $email) }}">
          @csrf
          @method('POST')
            <div class="form-group mb-4">
              <label for="email" class="custom-label">Email :</label>
              <input
                type="email"
                class="form-control custom-input @error('email') is-invalid @enderror"
                id="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="ex: exemple@mail.com"
                required
              >
              @error('email')
              <div class="text-danger" style="color: red; text-align:center">{{ $message }}</div>
              @enderror
            </div>
            <div class="form-group mb-4">
              <label for="text" class="custom-label">Code de Validation :</label>
              <input
                type="text"
                class="form-control custom-input @error('code') is-invalid @enderror"
                id="code"
                name="code"
                value="{{ old('code') }}"
                placeholder="veuillez entrer le code de validation"
                required
              >
              @error('code')
              <div class="text-danger" style="color: red; text-align:center">{{ $message }}</div>
              @enderror
            </div>
          
            <div class="form-group mb-4">
              <label for="password" class="custom-label">Mot de passe :</label>
              <input
                type="password"
                class="form-control custom-input @error('password') is-invalid @enderror"
                id="password"
                name="password"
                value="{{ old('password') }}"
                placeholder="************"
                required
              >
              @error('password')
              <div class="text-danger" style="color: red; text-align:center">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group mb-4">
              <label for="password" class="custom-label">Mot de passe :</label>
              <input
                type="password"
                class="form-control custom-input @error('password') is-invalid @enderror"
                id="password"
                name="password"
                value="{{ old('password') }}"
                placeholder="************"
                required
              >
              @error('password')
              <div class="text-danger" style="color: red; text-align:center">{{ $message }}</div>
              @enderror
            </div>
            <div class="text-center">
              <button type="submit" class="btn btn-primary submit-btn w-100">Valider</button>
            </div>
        </form>
      </div>
    </div>
  </main>
  <script src="{{ asset('js/app.js') }}"></script>
  @stack('plugin-scripts')
  <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
  <script src="{{ asset('assets/js/hoverable-collapse.js') }}"></script>
  <script src="{{ asset('assets/js/misc.js') }}"></script>
  <script src="{{ asset('assets/js/settings.js') }}"></script>
  <script src="{{ asset('assets/js/todolist.js') }}"></script>
  <script src="{{ asset('js/custom-sidebar.js') }}"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = { damping: '0.5' }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }

    // Script pour gérer l'état 'is-filled' des inputs Material Dashboard au chargement de la page
    // si des valeurs sont déjà présentes (ex: old input)
    document.addEventListener('DOMContentLoaded', function () {
        var inputs = document.querySelectorAll('.input-group.input-group-outline .form-control');
        inputs.forEach(function(input) {
            if (input.value && input.value.trim() !== '') {
                input.parentNode.classList.add('is-filled');
            }
            input.addEventListener('focus', function() {
                input.parentNode.classList.add('is-focused');
            });
            input.addEventListener('blur', function() {
                input.parentNode.classList.remove('is-focused');
                if (input.value && input.value.trim() !== '') {
                    input.parentNode.classList.add('is-filled');
                } else {
                    input.parentNode.classList.remove('is-filled');
                }
            });
        });
    });

  </script>
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script src="{{ asset('assets/assets/js/material-dashboard.min.js?v=3.2.0') }}"></script>
</body>
</html>






















<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/assets/img/apple-icon.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('assets/assets/img/favicon.png') }}">
  <title>
    VALIDATE - COMPTE - SEA
  </title>
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <!-- Nucleo Icons -->
  <link href="{{ asset('assets/assets/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <!-- CSS Files -->
  <link id="pagestyle" href="{{ asset('assets/assets/css/material-dashboard.css?v=3.2.0') }}" rel="stylesheet" />
</head>

<body class="">
  <div class="container position-sticky z-index-sticky top-0">
    <div class="row">
      <div class="col-12">
      </div>
    </div>
  </div>
  <main class="main-content  mt-0">
    <section>
      <div class="page-header min-vh-100">
        <div class="container">
          <div class="row">
            <div class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 start-0 text-center justify-content-center flex-column">
              <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center" style="background-image: url('{{ asset('assets/images/kkstevhno.jpeg') }}'); background-size: cover;">
              </div>
            </div>
            <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column ms-auto me-auto ms-lg-auto me-lg-5">
              <div class="card card-plain">
                <div class="card-header">
                  <h4 class="font-weight-bolder">Se connecter</h4>
                  <p class="mb-0">Entrez votre email et mot de passe pour vous connecter</p>
                </div>
                <div class="card-body">
                  <form role="form" method="POST" action="{{ route('admin.validate', $email) }}">
                    @csrf
                    @method('POST')

                    <div class="input-group input-group-outline mb-3">
                        <input type="email" class="form-control"  value="{{ $email }}" name="email" readonly>
                      </div>
                      @error('email')
                      <div class="text-danger" style="color: red; text-align:center">{{ $message }}</div>
                      @enderror

                    <div class="input-group input-group-outline mb-3">
                      <input type="text" class="form-control" value="{{ old('code') }}" placeholder="veuillez entrer le code de validation"  name="code">
                    </div>
                    @error('code')
                    <div class="text-danger" style="color: red; text-align:center">{{ $message }}</div>
                    @enderror

                    
                    <div class="input-group input-group-outline mb-3">
                      <label class="form-label">Mot de passe</label>
                      <input type="password" class="form-control" value="{{ old('password') }}" name="password">
                    </div>
                    @error('password')
                    <div class="text-danger" style="color: red; text-align:center">{{ $message }}</div>
                    @enderror
                    <div class="input-group input-group-outline mb-3">
                      <label class="form-label">Mot de passe</label>
                      <input type="password" class="form-control" value="{{ old('confirme_password') }}" name="confirme_password">
                    </div>
                    @error('confirme_password')
                    <div class="text-danger" style="color: red; text-align:center">{{ $message }}</div>
                    @enderror
                    <div class="text-center">
                      <button type="submit" class="btn btn-lg bg-gradient-dark btn-lg w-100 mt-4 mb-0">Valider</button>
                    </div>
                  </form>


                </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  <!--   Core JS Files   -->
  <script src="{{ asset('assets/assets/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('assets/assets/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('assets/assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="{{ asset('assets/assets/js/material-dashboard.min.js?v=3.2.0') }}"></script>
</body>

</html>
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
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">

</head>

<body class="login-page">

  <main class="main-content mt-0" style="background-image: url('{{ url('assets/images/auth/admin_login.png') }}');
                                      background-size: contain; background-repeat: no-repeat; background-position: 10% center;">
    <div class="auth-wrapper">
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
            {{-- <label for="email" class="custom-label">Email :</label> --}}
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
          {{-- Options supplémentaires (Se souvenir de moi, Mot de passe oublié) --}}
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

          {{-- <div class="signup-link-wrapper mt-4 text-center">
            Vous n'avez pas de compte ?
            <a href="#" class="text-primary text-gradient font-weight-bold signup-link">S'inscrire</a>
          </div> --}}
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
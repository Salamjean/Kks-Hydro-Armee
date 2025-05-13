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
  {{-- <link rel="shortcut icon" href="{{ asset('assetsSEA/images/favicon.svg" type="image/x-icon') }}"> --}}

  <link rel="stylesheet" href="{{ asset('css/login.css') }}">

</head>

<body class="login-page">

  <main class="main-content mt-0">
    <div class="auth-wrapper" style="background-image: url('{{ url('assets/images/auth/login_4.png') }}');">
      <div class="auth-card">
        {{-- <div class="auth-logo">
          <img src="{{ asset('assets/assets/img/favicon.png') }}" alt="Logo SEA">
        </div> --}}
        <h4 class="auth-title">Connexion Espace Soute</h4>
                    <p class="auth-subtitle mb-5">Connectez-vous avec vos identifiants.</p>

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
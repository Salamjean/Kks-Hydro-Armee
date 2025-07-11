<!DOCTYPE html>
<html lang="en">

<head>
    <title>
    VALIDATE - COMPTE - SEA
  </title>
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

                    <div class="form-group input-group-outline mb-3">
                        <input type="email" class="form-control"  value="{{ $email }}" name="email" readonly>
                      </div>
                      @error('email')
                      <div class="text-danger" style="color: red; text-align:center">{{ $message }}</div>
                      @enderror

                    <div class="form-group input-group-outline mb-3">
                      <input type="text" class="form-control" value="{{ old('code') }}" placeholder="veuillez entrer le code de validation"  name="code">
                    </div>
                    @error('code')
                    <div class="text-danger" style="color: red; text-align:center">{{ $message }}</div>
                    @enderror

                    
                    <div class="form-group input-group-outline mb-3">
                      <label class="custom-label">Mot de passe</label>
                      <input type="password" class="form-control" value="{{ old('password') }}" name="password">
                    </div>
                    @error('password')
                    <div class="text-danger" style="color: red; text-align:center">{{ $message }}</div>
                    @enderror
                    <div class="form-group input-group-outline mb-3">
                      <label class="custom-label">Mot de passe</label>
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
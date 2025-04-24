
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEA - Dashboard</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assetsSEA/css/bootstrap.css') }}">

    <link rel="stylesheet" href="{{ asset('assetsSEA/vendors/iconly/bold.css') }}">

    <link rel="stylesheet" href="{{ asset('assetsSEA/vendors/perfect-scrollbar/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('assetsSEA/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assetsSEA/css/app.css') }}">
    <link rel="shortcut icon" href="{{ asset('assetsSEA/images/favicon.svg" type="image/x-icon') }}">
</head>

<body>
    <div id="app">
        @include('admin.layouts.sidebar')
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <h3>Tableau de bord</h3>
            </div>
            @yield('content')

            
        </div>
    </div>
    <script src="{{ asset('assetsSEA/vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assetsSEA/js/bootstrap.bundle.min.js ') }}"></script>

    <script src="{{ asset('assetsSEA/vendors/apexcharts/apexcharts.js ') }}"></script>
    <script src="{{ asset('assetsSEA/js/pages/dashboard.js ') }}"></script>

    <script src="{{ asset('assetsSEA/js/main.js ') }}"></script>
</body>

</html>
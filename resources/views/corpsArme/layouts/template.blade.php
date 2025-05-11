<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title', "Gestion des soutes de l'Armée")</title>

    <link rel="shortcut icon" href="{{ asset('/favicon.ico') }}">

    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/@mdi/font/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    @stack('plugin-styles') 

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('styles')

</head>
<body data-base-url="{{url('/')}}">

    <div class="container-scroller" id="app">

        @include('corpsArme.layouts.partials.header')

        <div class="container-fluid page-body-wrapper">
            {{-- @include('corpsArme.layouts.sidebar') --}}

            <div class="main-panel">
                <div class="content-wrapper">
                   
                    @yield('content')
                </div>
                @include('corpsArme.layouts.partials.footer')
            </div>
        </div>
    </div>

    <!-- Base JS -->
    <script src="{{ asset('js/app.js') }}"></script>

    @stack('plugin-scripts')

    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('assets/js/misc.js') }}"></script>
    <script src="{{ asset('assets/js/settings.js') }}"></script>
    <script src="{{ asset('assets/js/todolist.js') }}"></script>

    @stack('custom-scripts') 
</body>
</html>
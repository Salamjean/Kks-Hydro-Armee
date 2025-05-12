@extends('corpsArme.armee-terre.template')

@section('title', 'Tableau de Bord - Armée de Terre')

@section('content')
<div class="page-heading">
  <h4>Bienvenue, {{ Auth::guard('corps')->user()->name }}</h4>
</div>
<div class="page-content">
    <section class="row">
        <div class="row">
          @php
            $stats = [
              ['icon' => 'mdi-cube', 'color' => 'text-danger', 'label' => 'Total Soutes Actives', 'value' => '120', 'footer' => 'Capacité totale: 2.5M L', 'footer_icon' => 'mdi-gas-cylinder'],
              ['icon' => 'mdi-receipt', 'color' => 'text-warning', 'label' => 'Carburant Distribué (Mois)', 'value' => '350K L', 'footer' => 'Augmentation de 5% vs M-1', 'footer_icon' => 'mdi-arrow-up-bold'],
              ['icon' => 'mdi-account-group', 'color' => 'text-success', 'label' => 'Pompistes Actifs', 'value' => '32', 'footer' => 'Sur 4 zones', 'footer_icon' => 'mdi-map-marker-radius'],
              ['icon' => 'mdi-alert-circle-outline', 'color' => 'text-info', 'label' => 'Alertes Niveau Bas', 'value' => '5', 'footer' => 'Nécessitent réapprovisionnement', 'footer_icon' => 'mdi-bell-ring']
            ];
          @endphp

          @foreach($stats as $stat)
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 grid-margin stretch-card">
              <div class="card card-statistics">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="float-left">
                        <i class="mdi {{ $stat['icon'] }} {{ $stat['color'] }} icon-lg"></i>
                    </div>
                    <div class="float-right text-right"> 
                      <p class="mb-0">{{ $stat['label'] }}</p>
                      <h3 class="font-weight-medium mb-0">{{ $stat['value'] }}</h3>
                    </div>
                  </div>
                  <p class="text-muted mt-3 mb-0 d-flex align-items-center"> 
                    <i class="mdi {{ $stat['footer_icon'] }} mr-1" aria-hidden="true"></i> {{ $stat['footer'] }}
                  </p>
                </div>
              </div>
            </div>
          @endforeach
        </div>
    </section>
</div>

@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/chartjs/chart.min.js') }}"></script>
@endpush

@push('custom-scripts')
  <script src="{{ asset('assets/js/dashboard.js') }}"></script>
@endpush
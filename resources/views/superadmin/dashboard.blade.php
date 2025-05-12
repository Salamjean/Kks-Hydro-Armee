{{-- resources/views/superadmin/dashboard.blade.php --}}
@extends('superadmin.layouts.template')

@section('title', 'Tableau de Bord - Armée Hydro')
@section('page-title', 'Tableau de Bord') {{-- Pour le titre dans la navbar --}}
@section('breadcrumb', 'Tableau de Bord') {{-- Pour le breadcrumb dans la navbar --}}

@section('content')
 <div class="row">
    {{-- Cartes Résumé - Responsive --}}
    {{-- Sur XL: 3 colonnes (col-xl-4) --}}
    {{-- Sur MD: 2 colonnes (col-md-6) --}}
    {{-- Sur SM et XS: 1 colonne (implicite) --}}
    <div class="col-xl-4 col-md-6 mb-xl-0 mb-4"> {{-- mb-4 pour l'espace vertical sur mobile, mb-xl-0 pour annuler sur desktop --}}
        <div class="card card-body border-radius-lg p-3"> {{-- Structure alternative simple --}}
            <div class="row">
                <div class="col-8">
                    <div class="numbers">
                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Essence</p>
                        <h5 class="font-weight-bolder mb-0">
                            53 m³ {{-- $essenceQuantity --}}
                            <span class="text-success text-sm font-weight-bolder ms-1">+55%</span> {{-- $essenceChange --}}
                        </h5>
                         <p class="text-xs text-secondary mb-0">que la semaine dernière</p> {{-- Texte descriptif --}}
                    </div>
                </div>
                <div class="col-4 text-end">
                    {{-- Icône stylisée --}}
                    <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                        <i class="material-icons-round text-lg opacity-10">local_gas_station</i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-xl-0 mb-4">
         <div class="card card-body border-radius-lg p-3">
            <div class="row">
                <div class="col-8">
                    <div class="numbers">
                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Diesel</p>
                        <h5 class="font-weight-bolder mb-0">
                            53 m³ {{-- $dieselQuantity --}}
                            <span class="text-success text-sm font-weight-bolder ms-1">+3%</span> {{-- $dieselChange --}}
                        </h5>
                        <p class="text-xs text-secondary mb-0">que le mois dernier</p>
                    </div>
                </div>
                <div class="col-4 text-end">
                    <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                         <i class="material-icons-round text-lg opacity-10">local_gas_station</i> {{-- Ou autre icone --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6"> {{-- Pas besoin de mb-4 sur le dernier pour MD --}}
         <div class="card card-body border-radius-lg p-3">
            <div class="row">
                <div class="col-8">
                    <div class="numbers">
                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Kérosène</p>
                        <h5 class="font-weight-bolder mb-0">
                            53 m³ {{-- $keroseneQuantity --}}
                            <span class="text-danger text-sm font-weight-bolder ms-1">-2%</span> {{-- $keroseneChange --}}
                        </h5>
                        <p class="text-xs text-secondary mb-0">qu'hier</p>
                    </div>
                </div>
                <div class="col-4 text-end">
                    <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                        <i class="material-icons-round text-lg opacity-10">opacity</i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Graphiques - Responsive --}}
<div class="row mt-4">
    {{-- Sur LG: 3 colonnes (col-lg-4) --}}
    {{-- Sur MD: 2 colonnes (col-md-6) --}}
    {{-- Sur SM et XS: 1 colonne (implicite) --}}
    <div class="col-lg-4 col-md-6 mt-4 mb-4">
        <div class="card z-index-2 ">
          <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
             <div class="bg-gradient-success shadow-success border-radius-lg py-3 pe-1">
              <div class="chart">
                <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
              </div>
            </div>
          </div>
          <div class="card-body">
            <h6 class="mb-0 ">Consommation Essence</h6>
            <p class="text-sm ">Performance mensuelle</p>
            <hr class="dark horizontal">
            <div class="d-flex ">
              <i class="material-icons-round text-sm my-auto me-1">schedule</i>
              <p class="mb-0 text-sm"> Mis à jour il y a 5 min </p> {{-- Texte dynamique --}}
            </div>
          </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 mt-4 mb-4">
        <div class="card z-index-2">
           <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
             <div class="bg-gradient-warning shadow-warning border-radius-lg py-3 pe-1">
                <div class="chart">
                  <canvas id="chart-line" class="chart-canvas" height="170"></canvas>
                </div>
              </div>
           </div>
          <div class="card-body">
            <h6 class="mb-0 "> Consommation Diesel </h6>
            <p class="text-sm "> Performance mensuelle </p>
            <hr class="dark horizontal">
            <div class="d-flex ">
              <i class="material-icons-round text-sm my-auto me-1">schedule</i>
              <p class="mb-0 text-sm"> Mis à jour il y a 10 min </p>
            </div>
          </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 mt-4 mb-lg-4 mb-md-0"> {{-- mb-md-0 pour annuler marge sur dernier élém. en 2 colonnes --}}
        <div class="card z-index-2">
           <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
             <div class="bg-gradient-info shadow-info border-radius-lg py-3 pe-1">
                <div class="chart">
                  <canvas id="chart-line-tasks" class="chart-canvas" height="170"></canvas>
                </div>
              </div>
           </div>
          <div class="card-body">
            <h6 class="mb-0 ">Consommation Kérosène</h6>
            <p class="text-sm ">Performance mensuelle</p>
            <hr class="dark horizontal">
            <div class="d-flex ">
              <i class="material-icons-round text-sm my-auto me-1">schedule</i>
              <p class="mb-0 text-sm"> Mis à jour il y a 1h </p>
            </div>
          </div>
        </div>
    </div>
</div>

{{-- Ajouter d'autres lignes (rows) et colonnes (cols) ici pour d'autres contenus --}}
{{-- Exemple: Une table pleine largeur --}}
{{-- <div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header pb-0">
                <h6>Dernières transactions</h6>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr> ... </tr>
                        </thead>
                        <tbody>
                            <tr> ... </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> --}}

@endsection

@push('scripts')
{{-- Scripts spécifiques à cette page (Initialisation des graphiques - même code que précédemment) --}}
<script>
    // Chart pour Essence (Barres) - Options adaptées au fond sombre
    var ctxBars = document.getElementById("chart-bars").getContext("2d");
    new Chart(ctxBars, {
      type: "bar",
      data: {
        labels: ["Jan", "Fév", "Mar", "Avr", "Mai", "Juin", "Juil", "Août", "Sep", "Oct", "Nov", "Déc"],
        datasets: [{
          label: "Volume (m³)", tension: 0.4, borderWidth: 0, borderRadius: 4, borderSkipped: false,
          backgroundColor: "rgba(255, 255, 255, .8)", // Blanc semi-transparent
          data: [50, 45, 22, 28, 50, 60, 76, 40, 65, 55, 70, 62], maxBarThickness: 6
        }, ],
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false, } },
        interaction: { intersect: false, mode: 'index', },
        scales: {
          y: {
            grid: { drawBorder: false, display: true, drawOnChartArea: true, drawTicks: false, borderDash: [5, 5], color: "rgba(255, 255, 255, .2)" }, // Grille blanche transparente
            ticks: { suggestedMin: 0, suggestedMax: 100, beginAtZero: true, padding: 10, font: { size: 14, weight: 300, family: "Inter", style: 'normal', lineHeight: 2 }, color: "#fff" } // Ticks blancs
          },
          x: {
            grid: { drawBorder: false, display: true, drawOnChartArea: true, drawTicks: false, borderDash: [5, 5], color: 'rgba(255, 255, 255, .2)' },
            ticks: { display: true, color: '#fff', padding: 10, font: { size: 14, weight: 300, family: "Inter", style: 'normal', lineHeight: 2 }, }
          },
        },
      },
    });

    // Chart pour Diesel (Ligne) - Options adaptées au fond sombre
    var ctxLine = document.getElementById("chart-line").getContext("2d");
    new Chart(ctxLine, {
      type: "line",
      data: {
        labels: ["Jan", "Fév", "Mar", "Avr", "Mai", "Juin", "Juil", "Août", "Sep", "Oct", "Nov", "Déc"],
        datasets: [{
          label: "Volume (m³)", tension: 0, borderWidth: 3, pointRadius: 5, pointBackgroundColor: "rgba(255, 255, 255, .8)", pointBorderColor: "transparent", borderColor: "rgba(255, 255, 255, .8)", // Ligne blanche
          fill: false, data: [120, 230, 130, 440, 250, 360, 270, 180, 90, 300, 310, 220], maxBarThickness: 6
        }],
      },
      options: {
        responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false, }, },
        interaction: { intersect: false, mode: 'index', },
        scales: {
          y: {
            grid: { drawBorder: false, display: true, drawOnChartArea: true, drawTicks: false, borderDash: [5, 5], color: 'rgba(255, 255, 255, .2)' },
            ticks: { display: true, color: '#fff', padding: 10, font: { size: 14, weight: 300, family: "Inter", style: 'normal', lineHeight: 2}, } },
          x: {
            grid: { drawBorder: false, display: false, drawOnChartArea: false, drawTicks: false, borderDash: [5, 5] },
            ticks: { display: true, color: '#fff', padding: 10, font: { size: 14, weight: 300, family: "Inter", style: 'normal', lineHeight: 2 }, } },
        },
      },
    });

     // Chart pour Kérosène (Ligne) - Options adaptées au fond sombre
    var ctxLineTasks = document.getElementById("chart-line-tasks").getContext("2d");
     new Chart(ctxLineTasks, {
      type: "line",
      data: {
        labels: ["Jan", "Fév", "Mar", "Avr", "Mai", "Juin", "Juil", "Août", "Sep", "Oct", "Nov", "Déc"],
        datasets: [{
          label: "Volume (m³)", tension: 0.4, borderWidth: 3, pointRadius: 5, pointBackgroundColor: "rgba(255, 255, 255, .8)", pointBorderColor: "transparent", borderColor: "rgba(255, 255, 255, .8)", // Ligne blanche
          fill: false, data: [50, 40, 30, 22, 50, 25, 40, 23, 50, 60, 70, 80], maxBarThickness: 6
        }],
      },
      options: {
        responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false, } },
        interaction: { intersect: false, mode: 'index', },
        scales: {
          y: {
            grid: { drawBorder: false, display: true, drawOnChartArea: true, drawTicks: false, borderDash: [5, 5], color: 'rgba(255, 255, 255, .2)' },
            ticks: { display: true, padding: 10, color: '#fff', font: { size: 14, weight: 300, family: "Inter", style: 'normal', lineHeight: 2 }, } },
          x: {
             grid: { drawBorder: false, display: false, drawOnChartArea: false, drawTicks: false, borderDash: [5, 5] },
            ticks: { display: true, color: '#fff', padding: 10, font: { size: 14, weight: 300, family: "Inter", style: 'normal', lineHeight: 2 }, } },
        },
      },
    });
</script>
@endpush
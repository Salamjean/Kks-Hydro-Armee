@extends('superadmin.layouts.template')
@section('content')
<div class="container-fluid py-2">
    <div class="row">
      <div class="ms-3">
        <h3 class="mb-0 h4 font-weight-bolder">Tableau de bord</h3>
        <p class="mb-4">
          Visualisez les quantités de carburant.
        </p>
      </div>
      <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-header p-2 ps-3">
            <div class="d-flex justify-content-between">
              <div>
                <p class="text-sm mb-0 text-capitalize">Essence</p>
                <h4 class="mb-0">53 m³</h4>
              </div>
              <div class="icon icon-md icon-shape  shadow-dark shadow text-center border-radius-lg" style="background-color: #4CAF50">
                <i class="material-icons">local_gas_station</i>
              </div>
            </div>
          </div>
          <hr class="dark horizontal my-0">
          <div class="card-footer p-2 ps-3">
            <p class="mb-0 text-sm"><span class="text-success font-weight-bolder">+55% </span>than last week</p>
          </div>
        </div>
      </div>
      <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-header p-2 ps-3">
            <div class="d-flex justify-content-between">
              <div>
                <p class="text-sm mb-0 text-capitalize">Diesel</p>
                <h4 class="mb-0">53 m³</h4>
              </div>
              <div class="icon icon-md icon-shape shadow-dark shadow text-center border-radius-lg" style="background-color: #FBC02D">
                <i class="material-symbols-rounded opacity-10">local_gas_station</i>
              </div>
            </div>
          </div>
          <hr class="dark horizontal my-0">
          <div class="card-footer p-2 ps-3">
            <p class="mb-0 text-sm"><span class="text-success font-weight-bolder">+3% </span>than last month</p>
          </div>
        </div>
      </div>
      <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-header p-2 ps-3">
            <div class="d-flex justify-content-between">
              <div>
                <p class="text-sm mb-0 text-capitalize">Kérosène</p>
                <h4 class="mb-0">53 m³</h4>
              </div>
              <div class="icon icon-md icon-shape shadow-dark shadow text-center border-radius-lg" style="background-color: #2196F3">
                <i class="material-symbols-rounded opacity-10">leaderboard</i>
              </div>
            </div>
          </div>
          <hr class="dark horizontal my-0">
          <div class="card-footer p-2 ps-3">
            <p class="mb-0 text-sm"><span class="text-danger font-weight-bolder">-2% </span>than yesterday</p>
          </div>
        </div>
      </div>
    <div class="row">
      <div class="col-lg-4 col-md-6 mt-4 mb-4">
        <div class="card">
          <div class="card-body">
            <h6 class="mb-0 ">Statistique de l'essence </h6>
            <p class="text-sm ">Selon les mois</p>
            <div class="pe-2">
              <div class="chart">
                <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
              </div>
            </div>
            <hr class="dark horizontal">
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6 mt-4 mb-4">
        <div class="card ">
          <div class="card-body">
            <h6 class="mb-0 "> Statistique du Diesel </h6>
            <p class="text-sm "> Selon les mois </p>
            <div class="pe-2">
              <div class="chart">
                <canvas id="chart-line" class="chart-canvas" height="170"></canvas>
              </div>
            </div>
            <hr class="dark horizontal">
          </div>
        </div>
      </div>
      <div class="col-lg-4 mt-4 mb-3">
        <div class="card">
          <div class="card-body">
            <h6 class="mb-0 ">Statistique du Kérosène</h6>
            <p class="text-sm ">Selon les mois</p>
            <div class="pe-2">
              <div class="chart">
                <canvas id="chart-line-tasks" class="chart-canvas" height="170"></canvas>
              </div>
            </div>
            <hr class="dark horizontal">
          </div>
        </div>
      </div>
    </div>
@endsection
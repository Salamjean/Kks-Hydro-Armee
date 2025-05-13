@extends('admin.layouts.template')

@section('title', 'Tableau de Bord')
@section('pageTitle', 'Tableau de Bord')
{{-- Optionnel: Breadcrumb --}}
{{-- @section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Tableau de Bord</li>
@endsection --}}

@section('content')
{{-- Le contenu existant des cartes et du graphique est parfait ici --}}
<section class="row">
    <div class="row">
        {{-- Carte 1 --}}
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-3 py-4-5"> {{-- Garde px-3 py-4-5 si c'est un style du template --}}
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start "> {{-- Ajustement des colonnes pour l'icône --}}
                            <div class="stats-icon purple mb-2">
                                <i class="iconly-boldShow"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Vues Profil</h6>
                            <h6 class="font-extrabold mb-0">112.000</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
         {{-- Carte 2 --}}
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                 <div class="card-body px-3 py-4-5">
                     <div class="row">
                         <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                            <div class="stats-icon blue mb-2">
                                <i class="iconly-boldProfile"></i>
                            </div>
                         </div>
                         <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Abonnés</h6>
                            <h6 class="font-extrabold mb-0">183.000</h6>
                         </div>
                     </div>
                 </div>
            </div>
        </div>
         {{-- Carte 3 --}}
        <div class="col-6 col-lg-3 col-md-6">
             <div class="card">
                 <div class="card-body px-3 py-4-5">
                     <div class="row">
                          <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                              <div class="stats-icon green mb-2">
                                  <i class="iconly-boldAdd-User"></i>
                              </div>
                          </div>
                          <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                              <h6 class="text-muted font-semibold">Abonnements</h6>
                              <h6 class="font-extrabold mb-0">80.000</h6>
                          </div>
                     </div>
                 </div>
             </div>
        </div>
         {{-- Carte 4 --}}
        <div class="col-6 col-lg-3 col-md-6">
             <div class="card">
                 <div class="card-body px-3 py-4-5">
                     <div class="row">
                         <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                            <div class="stats-icon red mb-2">
                                <i class="iconly-boldBookmark"></i>
                            </div>
                         </div>
                         <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Posts Enregistrés</h6>
                            <h6 class="font-extrabold mb-0">112</h6>
                         </div>
                     </div>
                 </div>
             </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Visites du Profil</h4>
                </div>
                <div class="card-body">
                    <div id="chart-profile-visit"></div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

{{-- Scripts spécifiques si besoin (ex: graphique) --}}
@push('custom-scripts')
    {{-- <script src="{{ asset('assetsSEA/vendors/apexcharts/apexcharts.js') }}"></script> --}}
    {{-- <script src="{{ asset('assetsSEA/js/pages/dashboard.js') }}"></script> --}}
    <script>
        console.log("Dashboard scripts loaded");
        // Ton code JS pour initialiser le graphique #chart-profile-visit ici
    </script>
@endpush
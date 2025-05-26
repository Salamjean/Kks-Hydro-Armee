@extends('pompiste.layouts.template')

@section('title', 'Rapport et statistiques')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--bootstrap-5 .select2-dropdown {
            z-index: 1060; 
        }
        
    </style>
@endpush

@section('content')
       {{-- Graphe combiné : Distribution & Dépotage --}}
        <div class="chart-container" style="width: 100%; margin: auto;">
            <h4 style="text-align: center;">Distribution et Dépotage (L)</h4>
            <canvas id="combinedChart" style="width: 100%; height: 50vh;"></canvas>
        </div>
        <hr>
        <!-- Formulaire de recherche par date -->
        <div>
            <h4 class="text-center">Recherche de Statistiques</h4>
            <p class="text-center">Sélectionnez une période pour voir les statistiques détaillées.</p>
            <form action="/recherche" method="GET" class="search-form">
                <div class="form-inline">
                    <div class="form-group">
                        <label for="date_debut">Date de début :</label>
                        <input type="date" id="date_debut" name="date_debut" required />
                    </div>

                    <div class="form-group">
                        <label for="date_fin">Date de fin :</label>
                        <input type="date" id="date_fin" name="date_fin" required />
                    </div>

                    <div class="form-group button-group">
                        <button type="submit" class="btn-search">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                    </div>
                </div>
            </form>
        </div>


    {{-- Affichage des statistiques Pompiste --}}
    @if($pompisteStats)
        <h4>Statistiques pour {{ $pompisteStats['pompiste']['nom'] }} - Année {{ $pompisteStats['annee'] }}
            @if($pompisteStats['mois'])
                - Mois de {{ $pompisteStats['mois'] }}
            @endif
        </h4>

        @if($pompisteStats['mois'] && $pompisteStats['stats_mois'])
            @php $statsMois = $pompisteStats['stats_mois']; @endphp
            <div class="card mb-3">
                <div class="card-header">Distributions par jour ({{ $pompisteStats['mois'] }} {{ $pompisteStats['annee'] }})</div>
                <div class="card-body">
                    @if($statsMois['distributions']->isNotEmpty())
                        <table class="table table-sm table-bordered table-striped mt-3">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Carburant</th>
                                    <th>Quantité Distribuée (L)</th>
                                    <th>Capacité Cuve (L)</th>
                                </tr>
                            </thead>
                            <tbody>
                            {{-- $statsMois['distributions'] est une collection d'objets --}}
                            @foreach($statsMois['distributions'] as $item)
                                <tr>
                                    <td>{{ $item->periode_obj->format('d/m/Y') }}</td>
                                    <td>{{ $item->type_carburant }}</td>
                                    <td>{{ number_format($item->total_quantite, 2) }}</td>
                                    <td>{{ number_format($item->capacite_totale_litres, 0) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>Aucune distribution pour ce pompiste ce mois-ci.</p>
                    @endif
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Dépotages par jour ({{ $pompisteStats['mois'] }} {{ $pompisteStats['annee'] }})</div>
                <div class="card-body">
                    @if($statsMois['depotages']->isNotEmpty())
                        <table class="table table-sm table-bordered table-striped mt-3">
                             <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Carburant</th>
                                    <th>Quantité Dépotée (L)</th>
                                    <th>Capacité Cuve (L)</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($statsMois['depotages'] as $item)
                                <tr>
                                    <td>{{ $item->periode_obj->format('d/m/Y') }}</td>
                                    <td>{{ $item->type_carburant }}</td>
                                    <td>{{ number_format($item->total_quantite, 2) }}</td>
                                    <td>{{ number_format($item->capacite_totale_litres, 0) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>Aucun dépotage pour ce pompiste ce mois-ci.</p>
                    @endif
                </div>
            </div>
        @endif

        {{-- Statistiques pour l'ANNÉE sélectionnée (agrégées par mois) --}}
        @if($pompisteStats['stats_annee'])
            @php $statsAnnee = $pompisteStats['stats_annee']; @endphp
            <div class="card mb-3">
                <div class="card-header">Distributions par mois (Année {{ $pompisteStats['annee'] }})</div>
                <div class="card-body">
                    @if($statsAnnee['distributions']->isNotEmpty())
                        <table class="table table-sm table-bordered table-striped mt-3">
                            <thead>
                                <tr>
                                    <th>Mois</th>
                                    <th>Carburant</th>
                                    <th>Quantité Distribuée (L)</th>
                                    <th>Capacité Cuve (L)</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($statsAnnee['distributions'] as $item)
                                <tr>
                                    <td>{{ $item->periode_obj->translatedFormat('F Y') }}</td>
                                    <td>{{ $item->type_carburant }}</td>
                                    <td>{{ number_format($item->total_quantite, 2) }}</td>
                                    <td>{{ number_format($item->capacite_totale_litres, 0) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>Aucune distribution pour ce pompiste cette année.</p>
                    @endif
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Dépotages par mois (Année {{ $pompisteStats['annee'] }})</div>
                <div class="card-body">
                     @if($statsAnnee['depotages']->isNotEmpty())
                        <table class="table table-sm table-bordered table-striped mt-3">
                            <thead>
                                <tr>
                                    <th>Mois</th>
                                    <th>Carburant</th>
                                    <th>Quantité Dépotée (L)</th>
                                    <th>Capacité Cuve (L)</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($statsAnnee['depotages'] as $item)
                                <tr>
                                     <td>{{ $item->periode_obj->translatedFormat('F Y') }}</td>
                                    <td>{{ $item->type_carburant }}</td>
                                    <td>{{ number_format($item->total_quantite, 2) }}</td>
                                    <td>{{ number_format($item->capacite_totale_litres, 0) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>Aucun dépotage pour ce pompiste cette année.</p>
                    @endif
                </div>
            </div>
        @endif

    @elseif(request()->has('pompiste_id'))
        <p class="alert alert-info">Veuillez sélectionner un pompiste et cliquer sur "Afficher Stats".</p>
    @endif

@endsection
<!-- Inclusion de Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('combinedChart');

        // Données passées depuis PHP via Blade
        const labels = @json($labels); // Mois
        const distributionData = @json($data); // Distribution
        const depotageData = @json($dataDepotage); // Dépotage

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Distribution (L)',
                        data: distributionData,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Dépotage (L)',
                        data: depotageData,
                        backgroundColor: 'rgba(255, 159, 64, 0.7)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: "Capacité (L)"
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: "Mois"
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                if (context.parsed.y !== null) label += context.parsed.y + ' L';
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });
</script>

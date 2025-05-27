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
            <form action="{{ route('soute.dashboard.rapports.index') }}" method="GET" class="search-form">
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
        <hr>
        <!-- Tableau des statistiques -->
        <div class="table-responsive">
            <h4 class="text-center">Statistiques de Distribution et Dépotage</h4>
            <table class="table table-striped table-bordered">
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('soute.dashboard.export.pdf') }}" class="btn btn-danger me-2">
                        <i class="bi bi-file-earmark-pdf-fill"></i> Télécharger PDF
                    </a>
                    <a href="{{ route('soute.dashboard.export.excel') }}" class="btn btn-success">
                        <i class="bi bi-file-earmark-excel-fill"></i> Télécharger Excel
                    </a>
                </div>
                <thead>
                    <tr>
                        <th>Mois</th>
                        <th>Distribution (L)</th>
                        <th>Dépotage (L)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($statistiques as $statistique)
                        <tr>
                            <td>{{ $statistique->mois }}</td>
                            <td>{{ $statistique->distribution }} L</td>
                            <td>{{ $statistique->depotage }} L</td>
                        </tr>
                    @endforeach
                </tbody>
        </table>

@endsection
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
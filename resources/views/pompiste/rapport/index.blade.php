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

@endsection
<!-- Inclusion de Chart.js -->
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

{{-- Dans resources/views/pompiste/rapport/index.blade.php --}}

@extends('pompiste.layouts.template')

@section('title', 'Rapport et statistiques')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--bootstrap-5 .select2-dropdown {
            z-index: 1060;
        }
        .search-form .form-inline { /* Pour un meilleur alignement du formulaire */
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end; /* Aligne le bas des éléments */
            gap: 1rem; /* Espace entre les groupes */
        }
        .search-form .form-group {
            margin-bottom: 0; /* Si Bootstrap ajoute des marges */
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid mt-3"> {{-- Ajout de container-fluid et marge --}}
        {{-- Graphe combiné : Distribution & Dépotage --}}
        <div class="card shadow-sm mb-4"> {{-- Ajout de card pour le style --}}
            <div class="card-header">
                <h5 class="mb-0">Aperçu des Distributions et Dépotages (Litres)
                    @if($pompisteStats['date_debut_filtre'] && $pompisteStats['date_fin_filtre'])
                        pour la période du {{ \Carbon\Carbon::parse($pompisteStats['date_debut_filtre'])->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($pompisteStats['date_fin_filtre'])->format('d/m/Y') }}
                    @else
                        pour l'année en cours
                    @endif
                </h5>
            </div>
            <div class="card-body">
                <div class="chart-container" style="width: 100%; margin: auto; position: relative; height:50vh;"> {{-- Style pour la hauteur du graphique --}}
                    <canvas id="combinedChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Formulaire de recherche par date -->
        <div class="card shadow-sm mb-4">
            <div class="card-header"><h5 class="mb-0">Recherche de Statistiques</h5></div>
            <div class="card-body">
                <p class="text-muted">Sélectionnez une période pour voir les statistiques détaillées.</p>
                {{-- L'action du formulaire doit pointer vers la route de la méthode rapport() --}}
                <form action="{{ route('soute.dashboard.rapports.index') }}" method="GET" class="search-form"> {{-- URL Corrigée --}}
                    <div class="form-inline">
                        <div class="form-group">
                            <label for="date_debut">Date de début :</label>
                            <input type="date" id="date_debut" name="date_debut" class="form-control" value="{{ $selectedDateDebut ?? old('date_debut') }}" /> {{-- `required` enlevé pour permettre de ne pas filtrer --}}
                        </div>

                        <div class="form-group">
                            <label for="date_fin">Date de fin :</label>
                            <input type="date" id="date_fin" name="date_fin" class="form-control" value="{{ $selectedDateFin ?? old('date_fin') }}" /> {{-- `required` enlevé --}}
                        </div>

                        <div class="form-group button-group">
                            <button type="submit" class="btn btn-primary"> {{-- Classe Bootstrap pour le style --}}
                                <i class="fas fa-search"></i> Rechercher
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tableau des statistiques -->
        <div class="card shadow-sm">
            <div class="card-header">
                 <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Statistiques Mensuelles
                        @if($pompisteStats['date_debut_filtre'] && $pompisteStats['date_fin_filtre'])
                            (Période filtrée)
                        @endif
                    </h5>
                    <div>
                        <a href="{{ route('soute.dashboard.export.pdf') }}" class="btn btn-danger btn-sm me-2">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                        <a href="{{ route('soute.dashboard.export.excel') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> Excel
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover"> {{-- Ajout table-hover --}}
                        <thead class="table-dark"> {{-- En-tête plus visible --}}
                            <tr>
                                <th>Mois</th>
                                <th>Distribution (L)</th>
                                <th>Dépotage (L)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($statistiques as $statistique)
                                <tr>
                                    <td>{{ $statistique->mois }}</td>
                                    <td>{{ number_format($statistique->distribution, 2) }} L</td>
                                    <td>{{ number_format($statistique->depotage, 2) }} L</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Aucune statistique à afficher pour la période sélectionnée.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
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

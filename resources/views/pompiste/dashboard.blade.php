@extends('pompiste.layouts.template')

@section('title', 'Tableau de Bord - Soute ' . ($soute->nom ?? 'Inconnue'))

@push('styles')
    <!-- Select2 CSS si utilisé pour modal -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Styles pour les jauges de carburant */
        .fuel-tank-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-family: Arial, sans-serif;
        }
        .fuel-tank {
            width: 180px;
            height: 300px;
            border: 3px solid #555;
            background-color: #e0e0e0;
            position: relative;
            border-radius: 10px 10px 5px 5px;
            margin: 0 auto;
            overflow: hidden;
        }
        .fuel-level {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            transition: height 0.5s ease-in-out, background-color 0.5s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 0.9em;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.7);
        }
        .graduations {
            position: absolute;
            top: 0;
            left: -35px;
            height: 100%;
            width: 30px;
            font-size: 0.7em;
            color: #333;
        }
        .graduation-mark {
            position: absolute;
            width: 100%;
            text-align: right;
            padding-right: 5px;
        }
        .graduation-mark::after {
            content: "—";
            position: absolute;
            right: -5px;
            top: 50%;
            transform: translateY(-50%);
        }
        .fuel-level.bg-success { background-color: #28a745 !important; }
        .fuel-level.bg-warning { background-color: #ffd607 !important; }
        .fuel-level.bg-danger  { background-color: #dc3545 !important; }
        .fuel-level.bg-secondary { background-color: #6c757d !important; }
        .bg-orange { background-color: #fd7e14 !important; }
        .tank-info {
            text-align: center;
            margin-top: 8px;
        }
        .tank-info p {
            margin-bottom: 2px;
            font-size: 0.9em;
        }
        /* Pour que Select2 apparaisse au-dessus du modal */
        .select2-container--bootstrap-5 .select2-dropdown {
            z-index: 1060;
        }
        .modal-dialog {
            max-width: 800px !important;
        }
        .select2-container {
            width: 100% !important;
        }
        .invalid-feedback {
            display: block;
        }
    </style>
@endpush

@section('content')
    {{-- En-tête --}}
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3>Tableau de Bord Soute : {{ $soute->nom ?? 'Inconnue' }}</h3>
                <p class="text-subtitle text-muted">
                    Matricule Soute : <strong>{{ $soute->matricule_soute ?? 'N/A' }}</strong>
                </p>
            </div>
            <div>
                <p class="text-end mb-0">Pompiste : {{ $personnel->nom_complet ?? 'Employé inconnu' }}</p>
                <p class="text-end text-muted mb-0">Matricule : {{ $personnel->matricule ?? '' }}</p>
            </div>
        </div>
    </div>

    {{-- Alerte générale si seuil indisponibilité ou alerte atteint --}}
    @isset($fuelsData)
        @php
            $alerteActive = false;
            $messagesAlertes = [];
            foreach ($fuelsData as $fuel) {
                $typeRaw = $fuel->type;
                $typeLower = strtolower($typeRaw);
                $niveauAffiche = $fuel->niveau_pour_affichage;
                $seuilAlerte = $soute->{"seuil_alert_$typeLower"} ?? null;
                $seuilIndispo = $soute->{"seuil_indisponibilite_$typeLower"} ?? null;
                if (!is_null($seuilIndispo) && $niveauAffiche <= $seuilIndispo) {
                    $alerteActive = true;
                    $messagesAlertes[] = "⚠ Le niveau de <strong>{$typeRaw}</strong> a atteint le seuil d'indisponibilité";
                }
                elseif (!is_null($seuilAlerte) && $niveauAffiche <= $seuilAlerte) {
                    $alerteActive = true;
                    $messagesAlertes[] = "⚠ Le niveau de <strong>{$typeRaw}</strong> est en-dessous du seuil d'alerte";
                }
            }
        @endphp

        @if($alerteActive)
            <div class="alert alert-danger shadow-sm mb-4 rounded-3 border border-danger-subtle">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-octagon-fill me-3 fs-3 text-danger"></i>
                    <div>
                        <h5 class="mb-1 fw-bold">Alerte de seuil critique !</h5>
                        @foreach($messagesAlertes as $msg)
                            <p class="mb-0">{!! $msg !!}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endisset

    {{-- Graphique Distribution / Dépotage --}}
    <div class="chart-container" style="width: 100%; margin: auto;">
        <h4 style="text-align: center;">Distribution et Dépotage (L)</h4>
        <canvas id="combinedChart" style="width: 100%; height: 30vh;"></canvas>
    </div>

    <div class="page-content">
        {{-- Section Stocks Carburant --}}
        <section class="section">
            <div class="row">
                <div class="col-12">
                    <h4 class="mb-3">État des Stocks Carburant</h4>
                </div>
            </div>
            <div class="row">
                @if(!empty($fuelsData))
                    @foreach($fuelsData as $fuel)
                        @php
                            $typeRaw = $fuel->type;
                            $typeLower = strtolower($typeRaw);
                            $capaciteMax = $fuel->capacite_totale;
                            if ($capaciteMax <= 0) { $capaciteMax = 1; }
                            $niveauAffiche = $fuel->niveau_pour_affichage;
                            $pourcentage = round(min(max(($niveauAffiche / $capaciteMax) * 100, 0), 100));
                            $seuilAlerte = $soute->{"seuil_alert_$typeLower"} ?? null;
                            $seuilIndisponibilite = $soute->{"seuil_indisponibilite_$typeLower"} ?? null;
                            $estIndisponible = !is_null($seuilIndisponibilite) && $niveauAffiche <= $seuilIndisponibilite;
                            $estEnAlerte = !is_null($seuilAlerte) && $niveauAffiche <= $seuilAlerte && !$estIndisponible;
                            if ($niveauAffiche <= 0) {
                                $fuelLevelColorClass = 'bg-secondary';
                                $pourcentage = 0;
                            }
                            elseif ($estIndisponible) {
                                $fuelLevelColorClass = 'bg-danger';
                            }
                            elseif ($estEnAlerte) {
                                $fuelLevelColorClass = 'bg-warning';
                            }
                            elseif ($pourcentage < 25) {
                                $fuelLevelColorClass = 'bg-danger';
                            }
                            elseif ($pourcentage < 50) {
                                $fuelLevelColorClass = 'bg-orange';
                            }
                            elseif ($pourcentage < 75) {
                                $fuelLevelColorClass = 'bg-warning';
                            }
                            elseif ($pourcentage < 100) {
                                $fuelLevelColorClass = 'bg-success';
                            }
                            else {
                                $fuelLevelColorClass = 'bg-primary';
                            }
                        @endphp

                        <div class="col-md-4 mb-4">
                            <div class="card h-100 
                                @if($estIndisponible) border-danger 
                                @elseif($estEnAlerte) border-warning 
                                @endif
                            ">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="{{ $fuel->icon_class }}"></i> {{ $typeRaw }}
                                    </h5>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                    <div class="fuel-tank-container mb-3">
                                        <div class="fuel-tank">
                                            <div class="fuel-level {{ $fuelLevelColorClass }}" style="height: {{ $pourcentage }}%;">
                                                @if($pourcentage > 0)
                                                    <span>{{ $pourcentage }}%</span>
                                                @endif
                                            </div>
                                            <div class="graduations">
                                                <div class="graduation-mark" style="bottom: 0%;">0%</div>
                                                <div class="graduation-mark" style="bottom: 25%;">25%</div>
                                                <div class="graduation-mark" style="bottom: 50%;">50%</div>
                                                <div class="graduation-mark" style="bottom: 75%;">75%</div>
                                                <div class="graduation-mark" style="top: 0; transform: translateY(-50%);">100%</div>
                                            </div>
                                        </div>
                                        <div class="tank-info mt-2">
                                            <p class="mb-0">Niveau: <strong>{{ number_format($niveauAffiche, 0, ',', ' ') }} L</strong></p>
                                            @if(!is_null($seuilAlerte))
                                                <p class="mb-0 @if($estEnAlerte) text-warning @endif">
                                                    Seuil alerte: {{ number_format($seuilAlerte, 0, ',', ' ') }} L
                                                </p>
                                            @endif
                                            @if(!is_null($seuilIndisponibilite))
                                                <p class="mb-0 @if($estIndisponible) text-danger @endif">
                                                    Seuil indisponibilité: {{ number_format($seuilIndisponibilite, 0, ',', ' ') }} L
                                                </p>
                                            @endif
                                            <p class="text-muted">Capacité: {{ number_format($fuel->capacite_totale, 0, ',', ' ') }} L</p>
                                        </div>
                                    </div>
                                    @if($fuel->capacite_totale > 0 && $niveauAffiche > $fuel->capacite_totale)
                                        <small class="text-danger d-block mt-1">
                                            <i class="bi bi-exclamation-triangle-fill"></i> Niveau actuel dépasse la capacité !
                                        </small>
                                    @elseif ($niveauAffiche > 0 && $fuel->capacite_totale <= 0)
                                        <small class="text-warning d-block mt-1">
                                            <i class="bi bi-exclamation-triangle-fill"></i> Capacité totale non définie.
                                        </small>
                                    @endif
                                </div>
                                {{-- <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Remplissage : {{ $pourcentage }}%</small>
                                </div> --}}
                                @if($estIndisponible)
                                <div class="card-footer bg-danger text-white">
                                    <i class="bi bi-exclamation-triangle-fill"></i> Seuil d'indisponibilité atteint
                                </div>
                            @elseif($estEnAlerte)
                                <div class="card-footer bg-danger text-white">
                                    <i class="bi bi-exclamation-triangle-fill"></i> Seuil d'alerte atteint
                                </div>
                            @endif
                            
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="alert alert-info text-center" role="alert">
                            @if(!isset($soute))
                                Les informations de la soute ne sont pas disponibles.
                            @elseif(empty($soute->types_carburants_stockes) || !is_array($soute->types_carburants_stockes))
                                Aucun type de carburant n'est configuré pour cette soute.
                            @else
                                Aucune donnée de carburant à afficher pour cette soute.
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </section>

        {{-- Section Informations Générales --}}
        <section class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h4>Informations Générales de la Soute</h4></div>
                    <div class="card-body">
                        <p><i class="bi bi-geo-alt-fill"></i> Localisation : {{ $soute->localisation ?? 'N/A' }}</p>
                        @if($soute->description)
                            <p><i class="bi bi-info-circle-fill"></i> Description : {{ $soute->description }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        {{-- Section Actions Rapides --}}
        <section class="section mt-4">
            <div class="card">
                <div class="card-header"><h4>Actions Rapides</h4></div>
                <div class="card-body">
                    <div class="d-grid gap-2 d-md-block">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#distributionModal">
                            <i class="bi bi-fuel-pump"></i> Faire une Distribution
                        </button>
                        {{-- Autres actions si besoin --}}
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- Modal Distribution --}}
    <div class="modal fade" id="distributionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="distributionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="maDistributionForm" action="{{ route('soute.dashboard.pompiste.store.distribution') }}" method="post">
                    @csrf
                    <input type="hidden" name="soute_id" id="soute_id_modal" value="{{ $soute->id ?? '' }}">

                    <div class="modal-header">
                        <h5 class="modal-title" id="distributionModalLabel">Nouvelle Distribution (Soute: {{ $soute->nom ?? '' }})</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Informations de stock et seuil d'indisponibilité --}}
                        <div class="alert alert-info">
                            <strong>Informations de stock & seuils :</strong>
                            <ul class="mb-0">
                                @if(in_array('Essence', $soute->types_carburants_stockes ?? []))
                                    <li>
                                        Essence : {{ number_format($soute->niveau_actuel_essence ?? 0, 2) }} L
                                        (seuil indisponibilité : {{ number_format($soute->seuil_indisponibilite_essence ?? 0, 2) }} L)
                                    </li>
                                @endif
                                @if(in_array('Diesel', $soute->types_carburants_stockes ?? []))
                                    <li>
                                        Diesel : {{ number_format($soute->niveau_actuel_diesel ?? 0, 2) }} L
                                        (seuil indisponibilité : {{ number_format($soute->seuil_indisponibilite_diesel ?? 0, 2) }} L)
                                    </li>
                                @endif
                                @if(in_array('Kerozen', $soute->types_carburants_stockes ?? []))
                                    <li>
                                        Kerozen : {{ number_format($soute->niveau_actuel_kerozen ?? 0, 2) }} L
                                        (seuil indisponibilité : {{ number_format($soute->seuil_indisponibilite_kerozen ?? 0, 2) }} L)
                                    </li>
                                @endif
                            </ul>
                            <p class="mt-2 mb-0">
                                La quantité distribuée ne doit pas faire descendre le stock au niveau du seuil d’indisponibilité ni en-dessous.
                            </p>
                        </div>

                        {{-- Messages session / validation --}}
                        @if (session('success_modal'))
                            <div class="alert alert-success">{{ session('success_modal') }}</div>
                        @endif
                        @if (session('error_modal'))
                            <div class="alert alert-danger">{{ session('error_modal') }}</div>
                        @endif
                        @if ($errors->any() && $errors->hasBag('distribution_modal'))
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->distribution_modal->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Champs formulaire --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nom_chauffeur_modal" class="form-label">Nom Complet du chauffeur *</label>
                                <input
                                    type="text"
                                    class="form-control @error('nom_chauffeur', 'distribution_modal') is-invalid @enderror"
                                    id="nom_chauffeur_modal"
                                    name="nom_chauffeur"
                                    placeholder="Nom et prénom"
                                    value="{{ old('nom_chauffeur') }}"
                                    required
                                >
                                @error('nom_chauffeur', 'distribution_modal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="immatriculation_vehicule_modal" class="form-label">Immatriculation du Véhicule *</label>
                                <input
                                    type="text"
                                    class="form-control @error('immatriculation_vehicule', 'distribution_modal') is-invalid @enderror"
                                    id="immatriculation_vehicule_modal"
                                    name="immatriculation_vehicule"
                                    placeholder="Ex: 1234AA12"
                                    value="{{ old('immatriculation_vehicule') }}"
                                    required
                                >
                                @error('immatriculation_vehicule', 'distribution_modal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="produit_modal" class="form-label">Type de Carburant *</label>
                                <select
                                    class="form-select @error('produit', 'distribution_modal') is-invalid @enderror"
                                    id="produit_modal"
                                    name="produit"
                                    required
                                >
                                    <option value="" disabled selected>Choisir...</option>
                                    {{-- Options remplies par JS --}}
                                </select>
                                @error('produit', 'distribution_modal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small id="stock_info_modal" class="form-text text-muted"></small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="quantite_modal" class="form-label">Quantité (Litres) *</label>
                                <input
                                    type="number"
                                    class="form-control @error('quantite', 'distribution_modal') is-invalid @enderror"
                                    id="quantite_modal"
                                    name="quantite"
                                    placeholder="Quantité en litres"
                                    value="{{ old('quantite') }}"
                                    required
                                    min="0.01"
                                    step="0.01"
                                >
                                @error('quantite', 'distribution_modal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date_depotage_modal" class="form-label">Date de Distribution *</label>
                                <input
                                    type="date"
                                    class="form-control @error('date_depotage', 'distribution_modal') is-invalid @enderror"
                                    id="date_depotage_modal"
                                    name="date_depotage"
                                    value="{{ old('date_depotage', date('Y-m-d')) }}"
                                    required
                                >
                                @error('date_depotage', 'distribution_modal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="heure_depotage_modal" class="form-label">Heure de Distribution *</label>
                                <input
                                    type="time"
                                    class="form-control @error('heure_depotage', 'distribution_modal') is-invalid @enderror"
                                    id="heure_depotage_modal"
                                    name="heure_depotage"
                                    value="{{ old('heure_depotage', date('H:i')) }}"
                                    required
                                >
                                @error('heure_depotage', 'distribution_modal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div> {{-- modal-body --}}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer la Distribution</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('custom-scripts')
    {{-- jQuery, Bootstrap JS, Select2 JS --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    $(document).ready(function() {
        const produitModalSelect = $('#produit_modal');
        const quantiteModalInput = $('#quantite_modal');
        const stockInfoModalSpan = $('#stock_info_modal');
        const distributionModalElement = document.getElementById('distributionModal');
        const distributionModal = new bootstrap.Modal(distributionModalElement);

        // Contexte de la soute pour JS
        const souteContext = {
            id: "{{ $soute->id ?? '' }}",
            typesDisponibles: @json($soute->types_carburants_stockes ?? []),
            niveauActuelEssence: {{ $soute->niveau_actuel_essence === null ? 'null' : (float)$soute->niveau_actuel_essence }},
            niveauActuelDiesel: {{ $soute->niveau_actuel_diesel === null ? 'null' : (float)$soute->niveau_actuel_diesel }},
            niveauActuelKerozen: {{ $soute->niveau_actuel_kerozen === null ? 'null' : (float)$soute->niveau_actuel_kerozen }},
            seuil_alert_essence: {{ $soute->seuil_alert_essence === null ? 'null' : (float)$soute->seuil_alert_essence }},
            seuil_alert_diesel: {{ $soute->seuil_alert_diesel === null ? 'null' : (float)$soute->seuil_alert_diesel }},
            seuil_alert_kerozen: {{ $soute->seuil_alert_kerozen === null ? 'null' : (float)$soute->seuil_alert_kerozen }},
            seuil_indisponibilite_essence: {{ $soute->seuil_indisponibilite_essence === null ? 'null' : (float)$soute->seuil_indisponibilite_essence }},
            seuil_indisponibilite_diesel: {{ $soute->seuil_indisponibilite_diesel === null ? 'null' : (float)$soute->seuil_indisponibilite_diesel }},
            seuil_indisponibilite_kerozen: {{ $soute->seuil_indisponibilite_kerozen === null ? 'null' : (float)$soute->seuil_indisponibilite_kerozen }},
        };
        const oldProduitModal = "{{ old('produit') }}";

        // Initialise Select2 (sera ré-initialisé à l'ouverture du modal)
        produitModalSelect.select2({
            placeholder: "Choisir un type de carburant",
            allowClear: true,
            theme: 'bootstrap-5'
        });

        function getStockDisponiblePourProduit(produitKey) {
            if (!produitKey) return 0;
            switch (produitKey.toLowerCase()) {
                case 'essence': return parseFloat(souteContext.niveauActuelEssence) || 0;
                case 'diesel': return parseFloat(souteContext.niveauActuelDiesel) || 0;
                case 'kerozen': return parseFloat(souteContext.niveauActuelKerozen) || 0;
                default: return 0;
            }
        }

        // Peupler options du select du modal
        function populateProduitsModal() {
            produitModalSelect.empty().append('<option value=""></option>');
            if (!souteContext.id || !Array.isArray(souteContext.typesDisponibles) || souteContext.typesDisponibles.length === 0) {
                produitModalSelect.append('<option value="" disabled selected>Aucun carburant disponible</option>');
                produitModalSelect.trigger('change');
                return;
            }
            souteContext.typesDisponibles.forEach(produitKeyValue => {
                if (typeof produitKeyValue !== 'string' || produitKeyValue.trim() === '') return;
                const key = produitKeyValue.toLowerCase();
                const affichage = produitKeyValue.charAt(0).toUpperCase() + produitKeyValue.slice(1);
                const stockTotal = getStockDisponiblePourProduit(produitKeyValue);
                const seuilIndispoRaw = souteContext[`seuil_indisponibilite_${key}`];
                const seuilIndispo = isNaN(parseFloat(seuilIndispoRaw)) ? null : parseFloat(seuilIndispoRaw);
                // Calcul max distribuable = stockTotal - seuilIndispo (ou stockTotal si seuilIndispo null)
                let maxDistrib = stockTotal;
                if (seuilIndispo !== null) {
                    maxDistrib = stockTotal - seuilIndispo;
                }
                if (maxDistrib < 0) maxDistrib = 0;
                const estIndisponible = (seuilIndispo !== null && stockTotal <= seuilIndispo) || (maxDistrib <= 0);

                let displayText = `${affichage} (Stock: ${stockTotal.toFixed(2)} L)`;
                const optionElement = $('<option>').val(produitKeyValue);
                if (estIndisponible) {
                    displayText += " - Indisponible";
                    optionElement.prop('disabled', true);
                } else {
                    displayText += ` - Distribuable max: ${maxDistrib.toFixed(2)} L`;
                }
                optionElement.text(displayText);
                produitModalSelect.append(optionElement);
            });
            if (oldProduitModal) {
                produitModalSelect.val(oldProduitModal);
            }
            produitModalSelect.trigger('change');
        }

        // Au changement de carburant sélectionné
        produitModalSelect.on('change', function() {
            const sel = $(this).val();
            stockInfoModalSpan.text('');
            quantiteModalInput.prop('disabled', false).removeAttr('max');
            if (sel) {
                const stockTotal = getStockDisponiblePourProduit(sel);
                const key = sel.toLowerCase();
                const seuilIndispoRaw = souteContext[`seuil_indisponibilite_${key}`];
                const seuilIndispo = isNaN(parseFloat(seuilIndispoRaw)) ? null : parseFloat(seuilIndispoRaw);
                let maxDistrib = stockTotal;
                if (seuilIndispo !== null) {
                    maxDistrib = stockTotal - seuilIndispo;
                }
                if (maxDistrib < 0) maxDistrib = 0;

                if (maxDistrib <= 0) {
                    stockInfoModalSpan.text("Distribution impossible : seuil d'indisponibilité atteint ou stock insuffisant.");
                    quantiteModalInput.prop('disabled', true);
                } else {
                    stockInfoModalSpan.text(`Quantité max distribuable : ${maxDistrib.toFixed(2)} L`);
                    quantiteModalInput.prop('disabled', false).attr('max', maxDistrib.toFixed(2));
                }
            }
        });

        // À l'ouverture du modal
        $('#distributionModal').on('show.bs.modal', function () {
            produitModalSelect.select2({
                dropdownParent: $(distributionModalElement),
                placeholder: "Choisir un type de carburant",
                allowClear: true,
                theme: 'bootstrap-5'
            });
            $('#soute_id_modal').val(souteContext.id);
            populateProduitsModal();
        });

        // À la fermeture du modal : reset
        $('#distributionModal').on('hidden.bs.modal', function () {
            $('#maDistributionForm')[0].reset();
            produitModalSelect.val(null).trigger('change');
            stockInfoModalSpan.text('');
            quantiteModalInput.prop('disabled', false).removeAttr('max');
            $('#maDistributionForm .is-invalid').removeClass('is-invalid');
            $('#maDistributionForm .invalid-feedback').text('');
            $('#distributionModal .alert-danger, #distributionModal .alert-success').remove();
        });

        // Forcer l’ouverture si erreurs validation côté back
        @if ($errors->any() && $errors->hasBag('distribution_modal'))
            distributionModal.show();
        @endif

        // Validation client avant envoi
        $('#maDistributionForm').on('submit', function(e) {
            const selectedProduit = produitModalSelect.val();
            const quantiteVal = parseFloat(quantiteModalInput.val());
            const maxAttr = parseFloat(quantiteModalInput.attr('max'));
            if (!selectedProduit) {
                alert('Veuillez sélectionner un type de carburant.');
                e.preventDefault(); return false;
            }
            if (quantiteModalInput.prop('disabled')) {
                alert('Distribution impossible pour ce carburant.');
                e.preventDefault(); return false;
            }
            if (isNaN(quantiteVal) || quantiteVal <= 0) {
                alert('La quantité doit être supérieure à zéro.');
                e.preventDefault(); return false;
            }
            if (!isNaN(maxAttr) && quantiteVal > maxAttr) {
                alert(`La quantité demandée (${quantiteVal.toFixed(2)} L) dépasse la quantité max distribuable (${maxAttr.toFixed(2)} L).`);
                e.preventDefault(); return false;
            }
            // sinon on laisse soumettre
        });
    });
    </script>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('combinedChart');
            if (ctx) {
                // Remplace par de vraies données si disponibles
                const labels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'];
                const distributionData = [1200, 1500, 1100, 1800, 1600, 1400];
                const depotageData = [1000, 1300, 900, 1700, 1500, 1200];
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
                            y: { beginAtZero: true, title: { display: true, text: "Capacité (L)" } },
                            x: { title: { display: true, text: "Mois" } }
                        },
                        plugins: {
                            legend: { display: true, position: 'top' },
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
            }
        });
    </script>
@endpush

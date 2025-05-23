@extends('pompiste.layouts.template')

@section('title', 'Gestion des Distributions - Soute ' . ($soute->nom ?? 'Inconnue'))

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--bootstrap-5 .select2-dropdown {
            z-index: 1060; /* Pour que Select2 apparaisse au-dessus du modal */
        }
        .modal-dialog {
            max-width: 800px !important;
        }
        .select2-container {
            width: 100% !important; /* S'assurer que Select2 prend toute la largeur */
        }
        .invalid-feedback {
            display: block; /* Pour que les messages d'erreur s'affichent sous les champs Select2 */
        }
    </style>
@endpush

@section('content')
<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3>Gestion des Distributions</h3>
            <p class="text-subtitle text-muted">Soute active : <strong>{{ $soute->nom ?? 'Non définie' }}</strong> (Matricule: {{ $soute->matricule_soute ?? 'N/A' }})</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#distributionModal">
            <i class="bi bi-fuel-pump"></i> Faire une Distribution
        </button>
    </div>
</div>

<!-- Modal Bootstrap -->
<div class="modal fade" id="distributionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="distributionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="maDistributionForm" action="{{ route('soute.dashboard.pompiste.store.distribution') }}" method="post">
                @csrf
                {{-- ID de la soute active, fourni par le contrôleur --}}
                <input type="hidden" name="soute_id" id="soute_id_modal" value="{{ $soute->id ?? '' }}">

                <div class="modal-header">
                    <h5 class="modal-title" id="distributionModalLabel">Nouvelle Distribution de Carburant (Soute: {{ $soute->nom ?? '' }})</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Affichage des erreurs générales ou de succès qui pourraient venir du redirect back --}}
                    @if (session('success_modal'))
                        <div class="alert alert-success">
                            {{ session('success_modal') }}
                        </div>
                    @endif
                    @if (session('error_modal'))
                        <div class="alert alert-danger">
                            {{ session('error_modal') }}
                        </div>
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


                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom_chauffeur_modal" class="form-label">Nom Complet du chauffeur *</label>
                            <input type="text" class="form-control @error('nom_chauffeur', 'distribution_modal') is-invalid @enderror" id="nom_chauffeur_modal" name="nom_chauffeur" placeholder="Nom et prénom" value="{{ old('nom_chauffeur') }}" required>
                            @error('nom_chauffeur', 'distribution_modal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="immatriculation_vehicule_modal" class="form-label">Immatriculation du Véhicule *</label>
                            <input type="text" class="form-control @error('immatriculation_vehicule', 'distribution_modal') is-invalid @enderror" id="immatriculation_vehicule_modal" name="immatriculation_vehicule" placeholder="Ex: 1234AA12" value="{{ old('immatriculation_vehicule') }}" required>
                            @error('immatriculation_vehicule', 'distribution_modal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="produit_modal" class="form-label">Type de Carburant *</label>
                            <select class="form-select @error('produit', 'distribution_modal') is-invalid @enderror" id="produit_modal" name="produit" required>
                                <option value="" disabled {{ old('produit') ? '' : 'selected' }}>Chargement...</option>
                                {{-- Options remplies par JavaScript --}}
                            </select>
                            @error('produit', 'distribution_modal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small id="stock_info_modal" class="form-text text-muted"></small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="quantite_modal" class="form-label">Quantité (Litres) *</label>
                            <input type="number" class="form-control @error('quantite', 'distribution_modal') is-invalid @enderror" id="quantite_modal" name="quantite" placeholder="Quantité en litres" value="{{ old('quantite') }}" required min="0.01" step="0.01">
                            @error('quantite', 'distribution_modal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date_depotage_modal" class="form-label">Date de Distribution *</label>
                            <input type="date" class="form-control @error('date_depotage', 'distribution_modal') is-invalid @enderror" id="date_depotage_modal" name="date_depotage" value="{{ old('date_depotage', date('Y-m-d')) }}" required>
                            @error('date_depotage', 'distribution_modal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="heure_depotage_modal" class="form-label">Heure de Distribution *</label>
                            <input type="time" class="form-control @error('heure_depotage', 'distribution_modal') is-invalid @enderror" id="heure_depotage_modal" name="heure_depotage" value="{{ old('heure_depotage', date('H:i')) }}" required>
                             @error('heure_depotage', 'distribution_modal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer la Distribution</button>
                </div>
            </form>
        </div>
    </div>
</div>

<section class="section mt-4">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Historique des Distributions (Soute: {{ $soute->nom ?? '' }})</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Pompiste</th>
                            <th>Chauffeur</th>
                            <th>Immatriculation</th>
                            <th>Type Carburant</th>
                            <th>Quantité (L)</th>
                            <th>Date & Heure</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($soute) && $soute->distributions()->exists()) {{-- Utiliser exists() est plus performant que count() > 0 ici --}}
                            @foreach($soute->distributions()->with('personnel')->latest()->take(10)->get() as $distribution)
                                <tr>
                                    <td>{{ $distribution->personnel->nom_complet ?? 'N/A' }}</td>
                                    <td>{{ $distribution->nom_chauffeur }}</td>
                                    <td>{{ $distribution->immatriculation_vehicule }}</td>
                                    <td>{{ ucfirst($distribution->type_carburant) }}</td>
                                    <td>{{ number_format($distribution->quantite, 2) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($distribution->date_depotage . ' ' . $distribution->heure_depotage)->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center">Aucune distribution enregistrée pour cette soute.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection

@push('custom-scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    {{-- Assurez-vous que Bootstrap JS est inclus, soit ici, soit dans votre layout principal --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    $(document).ready(function() {
        const produitModalSelect = $('#produit_modal');
        const quantiteModalInput = $('#quantite_modal');
        const stockInfoModalSpan = $('#stock_info_modal');
        const distributionModalElement = document.getElementById('distributionModal');
        const distributionModal = new bootstrap.Modal(distributionModalElement);

        produitModalSelect.select2({
            theme: "bootstrap-5",
            dropdownParent: $(distributionModalElement), // Correct pour Select2
            placeholder: "Choisir un type de carburant",
            allowClear: true
        });

        const souteContext = {
            id: "{{ $soute->id ?? null }}",
            typesDisponibles: @json($soute->types_carburants_stockes ?? []), // Ex: ["Diesel", "Kerozen", "Essence"]

            capaciteEssence: {{ (float)($soute->capacite_essence ?? 0) }},
            capaciteKerozen: {{ (float)($soute->capacite_kerozen ?? 0) }},
            capaciteDiesel: {{ (float)($soute->capacite_diesel ?? 0) }},

            // Important: Passer null explicitement si la valeur est null en PHP
            niveauActuelEssence: {{ $soute->niveau_actuel_essence === null ? 'null' : (float)$soute->niveau_actuel_essence }},
            niveauActuelKerozen: {{ $soute->niveau_actuel_kerozen === null ? 'null' : (float)$soute->niveau_actuel_kerozen }},
            niveauActuelDiesel: {{ $soute->niveau_actuel_diesel === null ? 'null' : (float)$soute->niveau_actuel_diesel }}
        };
        const oldProduitModal = "{{ old('produit') }}"; // Pour restaurer la sélection après une erreur de validation

        function getStockDisponiblePourProduit(produitKey) {
            let stock;
            // produitKey sera 'essence', 'kerozen', ou 'diesel' (minuscules)
            switch (produitKey) {
                case 'essence':
                    stock = (souteContext.niveauActuelEssence !== null) ? souteContext.niveauActuelEssence : souteContext.capaciteEssence;
                    break;
                case 'kerozen':
                    stock = (souteContext.niveauActuelKerozen !== null) ? souteContext.niveauActuelKerozen : souteContext.capaciteKerozen;
                    break;
                case 'diesel':
                    stock = (souteContext.niveauActuelDiesel !== null) ? souteContext.niveauActuelDiesel : souteContext.capaciteDiesel;
                    break;
                default:
                    stock = 0;
            }
            return parseFloat(stock) || 0;
        }

        function populateProduitsModal() {
            // console.log("Populating. Soute context:", souteContext);
            produitModalSelect.empty().append('<option value=""></option>'); // Pour le placeholder de Select2

            if (!souteContext.id) {
                 produitModalSelect.append('<option value="" disabled selected>Erreur: Soute non définie</option>');
                 produitModalSelect.trigger('change');
                 return;
            }

            let hasConfiguredProducts = false;

            // Les chaînes dans typesDisponibles sont "Essence", "Kerozen", "Diesel" (avec majuscule)
            // Les clés pour les options doivent être 'essence', 'kerozen', 'diesel' (minuscules) pour le backend
            souteContext.typesDisponibles.forEach(typeCarburantAffichage => { // ex: "Essence"
                let produitKeyValue = ''; // ex: 'essence'

                if (typeCarburantAffichage === 'Essence') produitKeyValue = 'essence';
                else if (typeCarburantAffichage === 'Kerozen') produitKeyValue = 'kerozen';
                else if (typeCarburantAffichage === 'Diesel') produitKeyValue = 'diesel';

                if (produitKeyValue) { // Si c'est un type de carburant que nous gérons
                    hasConfiguredProducts = true;
                    const stockDisponible = getStockDisponiblePourProduit(produitKeyValue);
                    let displayText = `${typeCarburantAffichage} (Stock: ${stockDisponible.toFixed(2)} L)`;
                    if (stockDisponible <= 0) {
                        displayText += " - Épuisé";
                    }
                    produitModalSelect.append(new Option(displayText, produitKeyValue));
                }
            });

            if (!hasConfiguredProducts && souteContext.id) {
                 produitModalSelect.append('<option value="" disabled selected>Aucun type de carburant géré configuré pour cette soute</option>');
            }

            if (oldProduitModal) {
                produitModalSelect.val(oldProduitModal);
            }
            produitModalSelect.trigger('change'); // Important pour Select2 et pour déclencher l'événement on-change
        }

        produitModalSelect.on('change', function() {
            const selectedProduitKey = $(this).val(); // 'essence', 'kerozen', ou 'diesel'
            let stockDisponible = 0;
            let estEpuise = true;

            if (selectedProduitKey) {
                stockDisponible = getStockDisponiblePourProduit(selectedProduitKey);
                estEpuise = (stockDisponible <= 0);
            }

            if (selectedProduitKey) {
                if (estEpuise) {
                    stockInfoModalSpan.text('Stock épuisé. Distribution impossible.');
                    quantiteModalInput.attr('max', 0).val(''); // Mettre à 0 et vider
                    quantiteModalInput.prop('disabled', true);
                } else {
                    stockInfoModalSpan.text(`Stock disponible: ${stockDisponible.toFixed(2)} L`);
                    quantiteModalInput.attr('max', stockDisponible.toFixed(2));
                    quantiteModalInput.prop('disabled', false);
                }
            } else { // Aucun produit sélectionné
                stockInfoModalSpan.text('');
                quantiteModalInput.removeAttr('max').val('');
                quantiteModalInput.prop('disabled', true);
            }
        });

        $('#distributionModal').on('show.bs.modal', function () {
            if (!souteContext.id && "{{ $soute->id ?? null }}") { // Au cas où $soute serait mis à jour par un autre script (peu probable ici)
                souteContext.id = "{{ $soute->id }}";
                $('#soute_id_modal').val("{{ $soute->id }}");
                // Potentiellement re-peupler souteContext si les autres valeurs peuvent changer
            }
            populateProduitsModal();
            // L'appel à trigger('change') dans populateProduitsModal s'occupe de la mise à jour initiale
        });

        $('#distributionModal').on('hidden.bs.modal', function () {
            $('#maDistributionForm')[0].reset();
            produitModalSelect.val(null).trigger('change'); // Réinitialise Select2 et sa logique
            // Le on-change videra stockInfoModalSpan et désactivera quantiteModalInput
            $('#maDistributionForm .is-invalid').removeClass('is-invalid');
            $('#maDistributionForm .invalid-feedback').text('');
            $('#distributionModal .alert-danger').remove();
            $('#distributionModal .alert-success').remove();
        });

        // Force l'ouverture du modal si des erreurs de validation pour ce modal sont présentes
        @if ($errors->any() && $errors->hasBag('distribution_modal'))
            distributionModal.show();
        @endif

        // Validation client basique avant soumission
        $('#maDistributionForm').on('submit', function(e) {
            const selectedProduit = produitModalSelect.val();
            const quantite = parseFloat(quantiteModalInput.val());

            if (!selectedProduit) {
                alert('Veuillez sélectionner un type de carburant.');
                e.preventDefault();
                return false;
            }
            if (quantiteModalInput.prop('disabled')) { // Implique stock épuisé
                alert('Le stock pour le carburant sélectionné est épuisé ou la quantité est invalide.');
                e.preventDefault();
                return false;
            }
            if (quantite <= 0) {
                alert('La quantité doit être supérieure à zéro.');
                e.preventDefault();
                return false;
            }
            // La vérification max est implicitement gérée par le fait que le champ est disabled si stock < quantité demandée (min 0.01)
            // ou si stock = 0. La validation serveur est la plus importante.
        });
    });
    </script>
@endpush
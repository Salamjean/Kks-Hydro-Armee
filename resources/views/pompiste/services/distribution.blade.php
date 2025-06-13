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
            <p class="text-subtitle text-muted">
                Soute active : <strong>{{ $soute->nom ?? 'Non définie' }}</strong>
                (Matricule: {{ $soute->matricule_soute ?? 'N/A' }})
            </p>
        </div>
        <!-- Bouton qui déclenche le modal -->
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
                    <h5 class="modal-title" id="distributionModalLabel">
                        Nouvelle Distribution de Carburant (Soute: {{ $soute->nom ?? '' }})
                    </h5>
                    <!-- Bouton Fermer -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Alerte si seuil atteint (côté back: méthode estEnAlerte) --}}
                    @if(
                        method_exists($soute, 'estEnAlerte') &&
                        (
                            $soute->estEnAlerte('diesel') ||
                            $soute->estEnAlerte('kerozen') ||
                            $soute->estEnAlerte('essence')
                        )
                    )
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            Attention : Seuil d'alerte atteint pour un ou plusieurs carburants
                        </div>
                    @endif

                    {{-- Affichage des messages de session --}}
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

                    {{-- Erreurs de validation spécifiques au modal (bag 'distribution_modal') --}}
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
                                <option value="" disabled selected>Chargement...</option>
                                {{-- Options remplies par JavaScript --}}
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
                        @if(isset($soute) && $soute->distributions()->exists())
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

@include('pompiste.layouts.partials._alert', ['errorBag' => 'distribution_modal'])
@endsection

@push('custom-scripts')
    {{-- jQuery (nécessaire pour Select2) --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    {{-- Bootstrap JS (vérifie que tu n'as pas de doublon) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    {{-- Select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    $(document).ready(function() {
        const produitModalSelect = $('#produit_modal');
        const quantiteModalInput = $('#quantite_modal');
        const stockInfoModalSpan = $('#stock_info_modal');
        const distributionModalElement = document.getElementById('distributionModal');
        // On instancie le modal Bootstrap pour usage en JS
        const distributionModal = new bootstrap.Modal(distributionModalElement);

        // Contexte de la soute pour JS
        const souteContext = {
            id: "{{ $soute->id ?? '' }}",
            typesDisponibles: @json($soute->types_carburants_stockes ?? []),
            // Niveaux actuels
            niveauActuelEssence: {{ $soute->niveau_actuel_essence === null ? 'null' : (float)$soute->niveau_actuel_essence }},
            niveauActuelKerozen: {{ $soute->niveau_actuel_kerozen === null ? 'null' : (float)$soute->niveau_actuel_kerozen }},
            niveauActuelDiesel: {{ $soute->niveau_actuel_diesel === null ? 'null' : (float)$soute->niveau_actuel_diesel }},
            // Seuils d'alerte (optionnel si besoin côté JS)
            seuil_alert_essence: {{ $soute->seuil_alert_essence === null ? 'null' : (float)$soute->seuil_alert_essence }},
            seuil_alert_kerozen: {{ $soute->seuil_alert_kerozen === null ? 'null' : (float)$soute->seuil_alert_kerozen }},
            seuil_alert_diesel: {{ $soute->seuil_alert_diesel === null ? 'null' : (float)$soute->seuil_alert_diesel }},
            // Seuils d'indisponibilité
            seuil_indisponibilite_essence: {{ $soute->seuil_indisponibilite_essence === null ? 'null' : (float)$soute->seuil_indisponibilite_essence }},
            seuil_indisponibilite_kerozen: {{ $soute->seuil_indisponibilite_kerozen === null ? 'null' : (float)$soute->seuil_indisponibilite_kerozen }},
            seuil_indisponibilite_diesel: {{ $soute->seuil_indisponibilite_diesel === null ? 'null' : (float)$soute->seuil_indisponibilite_diesel }},
        };
        const oldProduitModal = "{{ old('produit') }}";

        // Initialisation de Select2 sur le <select> (sans dropdownParent pour l'instant, sera ré-initialisé à l'ouverture)
        produitModalSelect.select2({
            placeholder: "Choisir un type de carburant",
            allowClear: true,
            theme: 'bootstrap-5'
        });

      // Fonction pour obtenir le stock disponible selon le type
function getStockDisponiblePourProduit(produitKey) {
    if (!produitKey) return 0;
    switch (produitKey.toLowerCase()) {
        case 'essence': return parseFloat(souteContext.niveauActuelEssence) || 0;
        case 'kerozen': return parseFloat(souteContext.niveauActuelKerozen) || 0;
        case 'diesel': return parseFloat(souteContext.niveauActuelDiesel) || 0;
        default: return 0;
    }
}

// Peupler les options du select de carburant dans le modal
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
        const typeCarburantAffichage = produitKeyValue.charAt(0).toUpperCase() + produitKeyValue.slice(1);
        const stockTotal = getStockDisponiblePourProduit(produitKeyValue);

        // Récupération du seuil d'indisponibilité
        const seuilIndispoRaw = souteContext[`seuil_indisponibilite_${key}`];
        const seuilIndispo = isNaN(parseFloat(seuilIndispoRaw)) ? null : parseFloat(seuilIndispoRaw);

        // Calcul de la quantité maximale distribuable
        let maxDistrib = stockTotal;
        if (seuilIndispo !== null) {
            maxDistrib = stockTotal - seuilIndispo;
        }
        // Arrondir/prendre au moins 0
        if (maxDistrib < 0) {
            maxDistrib = 0;
        }
        // On détermine si distribution possible
        const estIndisponible = (seuilIndispo !== null && stockTotal <= seuilIndispo) || (maxDistrib <= 0);

        // Création de l'élément <option>
        const optionElement = $('<option>').val(produitKeyValue);
        let displayText = `${typeCarburantAffichage} (Stock total: ${stockTotal.toFixed(2)} L)`;

        if (estIndisponible) {
            displayText += " - Indisponible";
            optionElement.prop('disabled', true);
        } else {
            // On affiche la quantité maximale distribuable
            displayText += ` - Distribuable max: ${maxDistrib.toFixed(2)} L`;
            // pas de disabled car on peut distribuer
        }
        optionElement.text(displayText);
        produitModalSelect.append(optionElement);
    });

    // Restaurer l'ancienne valeur si existait
    if (oldProduitModal) {
        produitModalSelect.val(oldProduitModal);
    }
    produitModalSelect.trigger('change');
}

// Lorsqu'on change le carburant sélectionné, mise à jour du message et activation/désactivation du champ quantité
produitModalSelect.on('change', function() {
    const selectedProduitKey = $(this).val();
    stockInfoModalSpan.text('');
    quantiteModalInput.prop('disabled', false);
    quantiteModalInput.removeAttr('max');
    if (selectedProduitKey) {
        const stockTotal = getStockDisponiblePourProduit(selectedProduitKey);
        const key = selectedProduitKey.toLowerCase();
        const seuilIndispoRaw = souteContext[`seuil_indisponibilite_${key}`];
        const seuilIndispo = isNaN(parseFloat(seuilIndispoRaw)) ? null : parseFloat(seuilIndispoRaw);

        let maxDistrib = stockTotal;
        if (seuilIndispo !== null) {
            maxDistrib = stockTotal - seuilIndispo;
        }
        if (maxDistrib < 0) {
            maxDistrib = 0;
        }

        if (maxDistrib <= 0) {
            // On ne peut rien distribuer ou stock déjà au seuil/pas au-dessus
            stockInfoModalSpan.text("Distribution impossible : seuil d'indisponibilité atteint ou stock trop faible.");
            quantiteModalInput.prop('disabled', true);
        } else {
            // On peut distribuer jusqu'à maxDistrib
            stockInfoModalSpan.text(`Quantité max distribuable : ${maxDistrib.toFixed(2)} L`);
            quantiteModalInput.prop('disabled', false);
            quantiteModalInput.attr('max', maxDistrib.toFixed(2));
        }
    }
});

// Lors de l'ouverture du modal
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

// Validation avant soumission du formulaire
$('#maDistributionForm').on('submit', function(e) {
    const selectedProduit = produitModalSelect.val();
    const quantiteVal = parseFloat(quantiteModalInput.val());
    const maxAttr = parseFloat(quantiteModalInput.attr('max'));

    if (!selectedProduit) {
        alert('Veuillez sélectionner un type de carburant.');
        e.preventDefault();
        return false;
    }
    if (quantiteModalInput.prop('disabled')) {
        alert('Distribution impossible pour le carburant sélectionné.');
        e.preventDefault();
        return false;
    }
    if (isNaN(quantiteVal) || quantiteVal <= 0) {
        alert('La quantité doit être supérieure à zéro.');
        e.preventDefault();
        return false;
    }
    if (!isNaN(maxAttr) && quantiteVal > maxAttr) {
        alert(`La quantité demandée (${quantiteVal.toFixed(2)} L) dépasse la quantité maximale distribuable (${maxAttr.toFixed(2)} L).`);
        e.preventDefault();
        return false;
    }
    // sinon, laisse soumettre
});

    });
    </script>
@endpush

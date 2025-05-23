@extends('pompiste.layouts.template')

@section('title', 'Enregistrer un Dépotage - Soute ' . ($soute->nom ?? 'Inconnue'))

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--bootstrap-5 .select2-dropdown {
            z-index: 1065; /* Un peu plus haut que le modal de distribution si les deux peuvent être ouverts */
        }
        .modal-dialog.modal-xl { /* Utiliser modal-xl pour un formulaire plus grand */
             max-width: 1140px;
        }
        .select2-container { width: 100% !important; }
        .invalid-feedback { display: block; }
        .form-section-title {
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-bottom: 1px solid #eee;
            padding-bottom: 0.25rem;
        }
    </style>
@endpush

@section('content')
<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3>Gestion des Dépotages</h3>
            <p class="text-subtitle text-muted">Soute active : <strong>{{ $soute->nom ?? 'Non définie' }}</strong> (Matricule: {{ $soute->matricule_soute ?? 'N/A' }})</p>
        </div>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#depotageModal">
            <i class="bi bi-box-arrow-in-down"></i> Faire un Dépotage
        </button>
    </div>
</div>

<!-- Modal Bootstrap pour Dépotage -->
<div class="modal fade" id="depotageModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="depotageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> {{-- modal-xl pour plus d'espace --}}
        <div class="modal-content">
            <form id="depotageForm" action="{{ route('soute.dashboard.pompiste.store.depotage') }}" method="post"> {{-- Nouvelle route à créer --}}
                @csrf
                <input type="hidden" name="soute_id" id="soute_id_depotage_modal" value="{{ $soute->id ?? '' }}">

                <div class="modal-header">
                    <h5 class="modal-title" id="depotageModalLabel">Faire un dépotage (Soute: {{ $soute->nom ?? '' }})</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Affichage des erreurs et succès pour ce modal --}}
                    @if (session('success_depotage_modal'))
                        <div class="alert alert-success">{{ session('success_depotage_modal') }}</div>
                    @endif
                    @if (session('error_depotage_modal'))
                        <div class="alert alert-danger">{{ session('error_depotage_modal') }}</div>
                    @endif
                    @if ($errors->any() && $errors->hasBag('depotage_modal'))
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->depotage_modal->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <h6 class="form-section-title">Informations Générales</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="date_depotage" class="form-label">Date de dépotage *</label>
                            <input type="date" class="form-control @error('date_depotage', 'depotage_modal') is-invalid @enderror" id="date_depotage" name="date_depotage" value="{{ old('date_depotage', date('Y-m-d')) }}" required>
                            @error('date_depotage', 'depotage_modal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="heure_depotage" class="form-label">Heure de dépotage *</label>
                            <input type="time" class="form-control @error('heure_depotage', 'depotage_modal') is-invalid @enderror" id="heure_depotage" name="heure_depotage" value="{{ old('heure_depotage', date('H:i')) }}" required>
                            @error('heure_depotage', 'depotage_modal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="nom_operateur" class="form-label">Nom de l'opérateur *</label>
                            <input type="text" class="form-control @error('nom_operateur', 'depotage_modal') is-invalid @enderror" id="nom_operateur" name="nom_operateur" value="{{ old('nom_operateur', $personnel->nom_complet ?? '') }}" required>
                            @error('nom_operateur', 'depotage_modal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <h6 class="form-section-title">Informations sur le Transporteur</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="nom_societe_transporteur" class="form-label">Nom de la Société *</label>
                            <input type="text" class="form-control @error('nom_societe_transporteur', 'depotage_modal') is-invalid @enderror" id="nom_societe_transporteur" name="nom_societe_transporteur" value="{{ old('nom_societe_transporteur') }}" required>
                            @error('nom_societe_transporteur', 'depotage_modal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="nom_chauffeur_transporteur" class="form-label">Nom du Chauffeur *</label>
                            <input type="text" class="form-control @error('nom_chauffeur_transporteur', 'depotage_modal') is-invalid @enderror" id="nom_chauffeur_transporteur" name="nom_chauffeur_transporteur" value="{{ old('nom_chauffeur_transporteur') }}" required>
                            @error('nom_chauffeur_transporteur', 'depotage_modal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="immatriculation_vehicule_transporteur" class="form-label">Immatriculation du Véhicule *</label>
                            <input type="text" class="form-control @error('immatriculation_vehicule_transporteur', 'depotage_modal') is-invalid @enderror" id="immatriculation_vehicule_transporteur" name="immatriculation_vehicule_transporteur" value="{{ old('immatriculation_vehicule_transporteur') }}" required>
                            @error('immatriculation_vehicule_transporteur', 'depotage_modal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <h6 class="form-section-title">Informations sur le Dépôt</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="produit_depotage" class="form-label">Produit *</label>
                            <select class="form-select @error('produit', 'depotage_modal') is-invalid @enderror" id="produit_depotage" name="produit" required>
                                <option value="" disabled selected>Choisir le produit</option>
                                {{-- Options remplies par JS basées sur soute->types_carburants_stockes --}}
                            </select>
                            @error('produit', 'depotage_modal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="volume_transporte_l" class="form-label">Volume Transporté (L) *</label>
                            <input type="number" class="form-control @error('volume_transporte_l', 'depotage_modal') is-invalid @enderror" id="volume_transporte_l" name="volume_transporte_l" value="{{ old('volume_transporte_l') }}" step="0.01" required>
                            @error('volume_transporte_l', 'depotage_modal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="numero_bon_livraison" class="form-label">N° Bon de Livraison</label>
                            <input type="text" class="form-control @error('numero_bon_livraison', 'depotage_modal') is-invalid @enderror" id="numero_bon_livraison" name="numero_bon_livraison" value="{{ old('numero_bon_livraison') }}">
                            @error('numero_bon_livraison', 'depotage_modal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <h6 class="form-section-title">Informations sur la Cuve de Réception (Soute {{ $soute->nom }})</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="niveau_avant_depotage_l" class="form-label">Niveau avant dépotage (L) *</label>
                            <input type="number" class="form-control @error('niveau_avant_depotage_l', 'depotage_modal') is-invalid @enderror" id="niveau_avant_depotage_l" name="niveau_avant_depotage_l" value="{{ old('niveau_avant_depotage_l') }}" step="0.01" required readonly> {{-- Sera rempli par JS --}}
                            <small id="capacite_info_depotage" class="form-text text-muted"></small>
                            @error('niveau_avant_depotage_l', 'depotage_modal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="volume_recu_l" class="form-label">Volume Reçu (L) *</label>
                            <input type="number" class="form-control @error('volume_recu_l', 'depotage_modal') is-invalid @enderror" id="volume_recu_l" name="volume_recu_l" value="{{ old('volume_recu_l') }}" step="0.01" required>
                             @error('volume_recu_l', 'depotage_modal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                     <div class="row">
                        <div class="col-12 mb-3">
                            <label for="observations_depotage" class="form-label">Observations</label>
                            <textarea class="form-control @error('observations', 'depotage_modal') is-invalid @enderror" id="observations_depotage" name="observations" rows="3">{{ old('observations') }}</textarea>
                            @error('observations', 'depotage_modal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Confirmer le Dépotage</button>
                </div>
            </form>
        </div>
    </div>
</div>

<section class="section mt-4">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Historique des Dépotages (Soute: {{ $soute->nom ?? '' }})</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Produit</th>
                            <th>Volume Reçu (L)</th>
                            <th>Opérateur</th>
                            <th>Transporteur</th>
                            <th>BL</th>
                            {{-- Plus de colonnes si besoin --}}
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Boucle pour afficher les dépotages existants --}}
                        @if(isset($soute) && $soute->depotages()->exists())
                            @foreach($soute->depotages()->latest()->take(10)->get() as $depotage)
                                <tr>
                                    <td>{{ $depotage->date_depotage->format('d/m/Y') }} {{ $depotage->heure_depotage }}</td>
                                    <td>{{ ucfirst($depotage->produit) }}</td>
                                    <td>{{ number_format($depotage->volume_recu_l, 2) }}</td>
                                    <td>{{ $depotage->nom_operateur }}</td>
                                    <td>{{ $depotage->nom_societe_transporteur }}</td>
                                    <td>{{ $depotage->numero_bon_livraison }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center">Aucun dépotage enregistré pour cette soute.</td>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            const produitDepotageSelect = $('#produit_depotage');
            const niveauAvantDepotageInput = $('#niveau_avant_depotage_l');
            const capaciteInfoSpan = $('#capacite_info_depotage');
            const depotageModalElement = document.getElementById('depotageModal');
            const depotageModal = new bootstrap.Modal(depotageModalElement);
            const volumeRecuInput = $('#volume_recu_l');
            const volumeTransporteInput = $('#volume_transporte_l'); // Ajout pour pré-remplir volume reçu
    
    
            produitDepotageSelect.select2({
                theme: "bootstrap-5",
                dropdownParent: $(depotageModalElement),
                placeholder: "Choisir le produit",
                allowClear: true
            });
    
            const souteDepotageContext = {
                id: "{{ $soute->id ?? null }}",
                typesConfigures: @json($soute->types_carburants_stockes ?? []),
    
                capaciteEssence: {{ (float)($soute->capacite_essence ?? 0) }},
                capaciteKerozen: {{ (float)($soute->capacite_kerozen ?? 0) }},
                capaciteDiesel: {{ (float)($soute->capacite_diesel ?? 0) }},
    
                niveauActuelEssence: {{ $soute->niveau_actuel_essence === null ? 'null' : (float)$soute->niveau_actuel_essence }},
                niveauActuelKerozen: {{ $soute->niveau_actuel_kerozen === null ? 'null' : (float)$soute->niveau_actuel_kerozen }},
                niveauActuelDiesel: {{ $soute->niveau_actuel_diesel === null ? 'null' : (float)$soute->niveau_actuel_diesel }}
            };
            const oldProduitDepotage = "{{ old('produit', '', 'depotage_modal') }}";
            const epsilon = 0.001; // Petite marge pour les comparaisons de flottants
    
            // Fonction pour déterminer le niveau actuel à utiliser pour les calculs
            // (niveau_actuel si non null, sinon 0 car on ajoute à une soute potentiellement vide avant ce dépotage)
            function getNiveauCourantPourCalculDepotage(produitKey) {
                switch (produitKey) {
                    case 'essence':
                        return (souteDepotageContext.niveauActuelEssence !== null) ? souteDepotageContext.niveauActuelEssence : 0;
                    case 'kerozen':
                        return (souteDepotageContext.niveauActuelKerozen !== null) ? souteDepotageContext.niveauActuelKerozen : 0;
                    case 'diesel':
                        return (souteDepotageContext.niveauActuelDiesel !== null) ? souteDepotageContext.niveauActuelDiesel : 0;
                    default: return 0;
                }
            }
    
            function getCapaciteTotalePourProduit(produitKey) {
                switch (produitKey) {
                    case 'essence': return souteDepotageContext.capaciteEssence;
                    case 'kerozen': return souteDepotageContext.capaciteKerozen;
                    case 'diesel': return souteDepotageContext.capaciteDiesel;
                    default: return 0;
                }
            }
    
            function populateProduitsDepotage() {
                produitDepotageSelect.empty().append('<option value=""></option>');
                if (!souteDepotageContext.id) { /* ... */ return; }
    
                souteDepotageContext.typesConfigures.forEach(typeAffichage => {
                    let produitKeyValue = '';
                    if (typeAffichage === 'Essence') produitKeyValue = 'essence';
                    else if (typeAffichage === 'Kerozen') produitKeyValue = 'kerozen';
                    else if (typeAffichage === 'Diesel') produitKeyValue = 'diesel';
    
                    if (produitKeyValue) {
                        // Ajout d'un data-attribute pour stocker la capacité et le niveau courant
                        const option = new Option(typeAffichage, produitKeyValue);
                        $(option).data('capacite', getCapaciteTotalePourProduit(produitKeyValue));
                        $(option).data('niveau-courant', getNiveauCourantPourCalculDepotage(produitKeyValue));
                        produitDepotageSelect.append(option);
                    }
                });
    
                if (oldProduitDepotage) {
                    produitDepotageSelect.val(oldProduitDepotage);
                }
                produitDepotageSelect.trigger('change');
            }
    
            produitDepotageSelect.on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const selectedProduitKey = $(this).val();
                let niveauCourant = 0; // Niveau avant ce dépotage spécifique
                let capaciteTotale = 0;
                let espaceDisponible = 0;
    
                if (selectedProduitKey) {
                    niveauCourant = parseFloat(selectedOption.data('niveau-courant')) || 0; // Utilise le data attribute
                    capaciteTotale = parseFloat(selectedOption.data('capacite')) || 0;      // Utilise le data attribute
                    espaceDisponible = capaciteTotale - niveauCourant;
    
                    niveauAvantDepotageInput.val(niveauCourant.toFixed(2)); // Affiche le niveau avant dépotage
    
                    if (capaciteTotale <= 0) {
                        capaciteInfoSpan.text(`Capacité totale non définie pour ce produit. Dépotage risqué.`);
                        volumeRecuInput.prop('disabled', false).val(''); // Permettre la saisie mais informer
                    } else if (espaceDisponible <= epsilon) { // Si l'espace disponible est quasi nul ou négatif (déjà plein)
                        capaciteInfoSpan.html(`<strong class="text-danger">Soute pleine pour ce produit (Capacité: ${capaciteTotale.toFixed(2)} L). Dépotage impossible.</strong>`);
                        volumeRecuInput.prop('disabled', true).val('');
                    } else {
                        capaciteInfoSpan.text(`Capacité totale: ${capaciteTotale.toFixed(2)} L. Espace disponible: env. ${espaceDisponible.toFixed(2)} L.`);
                        volumeRecuInput.prop('disabled', false).val('');
                        // Pré-remplir "Volume Reçu" avec "Volume Transporté" si celui-ci a une valeur
                        const volTransporte = parseFloat(volumeTransporteInput.val());
                        if (!isNaN(volTransporte) && volTransporte > 0) {
                            // Limiter au volume transporté ou à l'espace disponible, le plus petit des deux
                            volumeRecuInput.val(Math.min(volTransporte, espaceDisponible).toFixed(2));
                        }
                    }
                } else {
                    niveauAvantDepotageInput.val('');
                    capaciteInfoSpan.text('');
                    volumeRecuInput.prop('disabled', true).val('');
                }
            });
    
            // Optionnel: si le volume transporté change, on peut aussi ajuster le volume reçu suggéré
            volumeTransporteInput.on('input', function() {
                const selectedProduitKey = produitDepotageSelect.val();
                if (selectedProduitKey && !volumeRecuInput.prop('disabled')) {
                    const volTransporte = parseFloat($(this).val());
                    const niveauCourant = parseFloat(produitDepotageSelect.find('option:selected').data('niveau-courant')) || 0;
                    const capaciteTotale = parseFloat(produitDepotageSelect.find('option:selected').data('capacite')) || 0;
                    const espaceDisponible = capaciteTotale - niveauCourant;
    
                    if (!isNaN(volTransporte) && volTransporte > 0 && espaceDisponible > epsilon) {
                        volumeRecuInput.val(Math.min(volTransporte, espaceDisponible).toFixed(2));
                    }
                }
            });
    
    
            $(depotageModalElement).on('show.bs.modal', function () {
                populateProduitsDepotage();
            });
    
            $(depotageModalElement).on('hidden.bs.modal', function () {
                $('#depotageForm')[0].reset();
                produitDepotageSelect.val(null).trigger('change'); // Cela va aussi réinitialiser les champs dépendants
                $('#depotageForm .is-invalid').removeClass('is-invalid');
                $('#depotageForm .invalid-feedback').text('');
                $(depotageModalElement).find('.alert-danger, .alert-success').remove();
            });
    
            @if ($errors->any() && $errors->hasBag('depotage_modal'))
                depotageModal.show();
            @endif
    
            $('#depotageForm').on('submit', function(e){
                const selectedProduitKey = produitDepotageSelect.val();
                const volumeRecu = parseFloat(volumeRecuInput.val());
    
                if(!selectedProduitKey){
                    alert('Veuillez sélectionner un produit.');
                    e.preventDefault(); return;
                }
    
                // Vérifier si le champ volume_recu_l est désactivé (ce qui signifie que la soute est pleine)
                if (volumeRecuInput.prop('disabled')) {
                    alert('La soute est pleine pour le produit sélectionné ou le produit est invalide. Dépotage impossible.');
                    e.preventDefault();
                    return;
                }
    
                if(isNaN(volumeRecu) || volumeRecu <= 0){
                    alert('Veuillez saisir un volume reçu valide et positif.');
                    e.preventDefault(); return;
                }
    
                const niveauCourant = parseFloat(produitDepotageSelect.find('option:selected').data('niveau-courant')) || 0;
                const capaciteTotale = parseFloat(produitDepotageSelect.find('option:selected').data('capacite')) || 0;
    
                // La confirmation de dépassement est toujours utile si l'utilisateur a pu forcer une valeur
                if (capaciteTotale > 0 && (niveauCourant + volumeRecu) > (capaciteTotale + epsilon)) { // Ajout d'epsilon pour la comparaison
                    if (!confirm(`Attention: Le volume reçu (${volumeRecu.toFixed(2)} L) ajouté au niveau actuel (${niveauCourant.toFixed(2)} L) dépassera la capacité totale de la cuve (${capaciteTotale.toFixed(2)} L).\nNouveau niveau estimé: ${(niveauCourant + volumeRecu).toFixed(2)} L.\nVoulez-vous continuer quand même ?`)) {
                        e.preventDefault();
                        return;
                    }
                }
            });
        });
        </script>
    @endpush
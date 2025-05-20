@extends('pompiste.layouts.template')


@section('title', 'Liste des Services')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--bootstrap-5 .select2-dropdown {
            z-index: 1060; 
        }
        .modal-dialog {
            max-width: 1000px !important;
        }
    </style>
@endpush

@section('content')
    <!-- Bouton pour déclencher le modal -->
    <div class="card-header">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#distributionModal">
            <i class="bi bi-fuel-pump"></i> Faire un dépotage
        </button>
    </div>
    <!-- Modal Bootstrap -->
    <div class="modal fade" id="distributionModal" tabindex="-1" aria-labelledby="distributionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-x"></i></button>
                </div>
                <form id="distributionForm" action="" method="post">
                    @csrf
                    <div class="modal-header justify-content-center position-relative">
                        <h4 class="modal-title fs-5 text-center" id="distributionModalLabel">Faire un dépotage</h4>
                    </div>
                    <div class="modal-body">
                        <div id="form-errors" class="alert alert-danger d-none" role="alert"></div>

                        <fieldset class="mb-4">
                            <legend class="fs-6 fw-semibold border-bottom pb-2 mb-3 text-center">Informations Générales</legend>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="date_depotage" class="form-label">Date de dépotage *</label>
                                    <input type="date" class="form-control" id="date_depotage" name="date_depotage" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="heure_depotage" class="form-label">Heure de dépotage *</label>
                                    <input type="time" class="form-control" id="heure_depotage" name="heure_depotage" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="nom_operateur" class="form-label">Nom de l'opérateur *</label>
                                    <input type="text" class="form-control" id="nom_operateur" name="nom_operateur" placeholder="Nom de l'opérateur" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="mb-4">
                            <legend class="fs-6 fw-semibold border-bottom pb-2 mb-3 text-center">Informations sur le Transporteur</legend>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="nom_societe" class="form-label">Nom de la Société *</label>
                                    <input type="text" class="form-control" id="nom_societe" name="nom_societe" placeholder="Nom de la société" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="nom_chauffeur" class="form-label">Nom du Chauffeur *</label>
                                    <input type="text" class="form-control" id="nom_chauffeur" name="nom_chauffeur" placeholder="Nom du chauffeur" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="immatriculation_vehicule" class="form-label">Immatriculation du Véhicule *</label>
                                    <input type="text" class="form-control" id="immatriculation_vehicule" name="immatriculation_vehicule" placeholder="Numéro d'immatriculation" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="mb-4">
                            <legend class="fs-6 fw-semibold border-bottom pb-2 mb-3 text-center">Informations sur le Dépôt</legend>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="produit" class="form-label">Produit *</label>
                                    <select class="form-select" id="produit" name="produit" required>
                                        <option value="" disabled selected>Choisir le produit</option>
                                        <option value="essence">Essence</option>
                                        <option value="gasoil">Gasoil</option>
                                        <option value="diesel">Diesel</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="volume_transporte" class="form-label">Volume Transporté (L) *</label>
                                    <input type="number" step="any" class="form-control" id="volume_transporte" name="volume_transporte" placeholder="Ex: 10000" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="bon_livraison" class="form-label">N° Bon de Livraison *</label>
                                    <input type="text" class="form-control" id="bon_livraison" name="bon_livraison" placeholder="N° Bon de Livraison" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset>
                            <legend class="fs-6 fw-semibold border-bottom pb-2 mb-3 text-center">Informations sur la Cuve de Réception</legend>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="niveau_avant_depotage" class="form-label">Niveau avant dépotage (mm) *</label>
                                    <input type="number" step="any" class="form-control" id="niveau_avant_depotage" name="niveau_avant_depotage" placeholder="Ex: 1200" readonly>   
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="volume_recu" class="form-label">Volume Reçu (L) *</label>
                                    <input type="number" step="any" class="form-control" id="volume_recu" name="volume_recu" placeholder="Ex: 9950" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary">Confirmer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Liste des Dépotages --}}
    <section>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Liste des Dépotages</h4> {{-- J'ai renommé pour correspondre au formulaire --}}
            </div>
            <div class="card-body">
                <table class="table table-striped" id="depotagesTable"> {{-- Ajout d'un ID pour la mise à jour --}}
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Heure</th>
                            <th>Opérateur</th>
                            <th>Chauffeur</th>
                            <th>Immatriculation</th>
                            <th>Produit</th>
                            <th>Volume Reçu (L)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Remplir avec les données de la base de données -->
                        {{-- Exemple de ligne --}}
                        {{--
                        <tr>
                            <td>2023-10-27</td>
                            <td>10:00</td>
                            <td>Op. Alpha</td>
                            <td>Ch. Bravo</td>
                            <td>AB-123-CD</td>
                            <td>Gasoil</td>
                            <td>9950</td>
                            <td><button class="btn btn-sm btn-info">Détails</button></td>
                        </tr>
                        --}}
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

@push('custom-scripts')
    {{-- jQuery, Bootstrap JS, Select2 JS, SweetAlert2 --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>

    </script>
@endpush
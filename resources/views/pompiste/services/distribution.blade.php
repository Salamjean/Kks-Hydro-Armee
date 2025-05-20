@extends('pompiste.layouts.template')


@section('title', 'Liste des Services')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--bootstrap-5 .select2-dropdown {
            z-index: 1060; 
        }
        .modal-dialog {
            max-width: 800px !important;
        }
    </style>
@endpush

@section('content')
<div class="card-header d-flex justify-content-between align-items-center">
    <h4 class="card-title mb-0">Gestion des Distributions</h4>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#distributionModal">
        <i class="bi bi-fuel-pump"></i> Faire une Distribution
    </button>
</div>

<!-- Modal Bootstrap -->
<div class="modal fade" id="distributionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="distributionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="maDistributionForm" action="" method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="distributionModalLabel">Nouvelle Distribution de Carburant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> <i class="bi bi-x"></i></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom_chauffeur" class="form-label">Nom Complet du chauffeur *</label>
                            <input type="text" class="form-control" id="nom_chauffeur" name="nom_chauffeur" placeholder="Nom et prénom" required>
                            <div class="invalid-feedback">Veuillez saisir le nom du chauffeur.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="immatriculation_vehicule" class="form-label">Immatriculation du Véhicule *</label>
                            <input type="text" class="form-control" id="immatriculation_vehicule" name="immatriculation_vehicule" placeholder="Ex: 1234AA12" required>
                            <div class="invalid-feedback">Veuillez saisir l'immatriculation.</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="produit" class="form-label">Type de Carburant *</label>
                            <select class="form-select" id="produit" name="produit" required>
                                <option value="" disabled selected>Choisir un type de carburant</option>
                                <option value="essence">Essence</option>
                                <option value="gasoil">Gasoil</option>
                                <option value="diesel">Diesel</option>
                            </select>
                            <div class="invalid-feedback">Veuillez sélectionner un type de carburant.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="quantite" class="form-label">Quantité (Litres) *</label>
                            <input type="number" class="form-control" id="quantite" name="quantite" placeholder="Quantité en litres" required min="0.01" step="0.01">
                            <div class="invalid-feedback">Veuillez saisir une quantité valide.</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date_depotage" class="form-label">Date de dépotage *</label>
                            <input type="date" class="form-control" id="date_depotage" name="date_depotage" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="heure_depotage" class="form-label">Heure de dépotage *</label>
                            <input type="time" class="form-control" id="heure_depotage" name="heure_depotage" required>
                            <div class="invalid-feedback"></div>
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
    <section>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Liste des Services</h4>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nom du Chauffeur</th>
                            <th>Immatriculation</th>
                            <th>Type de Carburant</th>
                            <th>Quantité (L)</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

@push('custom-scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>

    </script>
@endpush
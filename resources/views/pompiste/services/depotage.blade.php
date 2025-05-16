@extends('pompiste.layouts.template')
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

@section('title', 'Liste des Services')

@section('content')
<!-- Bouton pour déclencher le modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
    Distribution
</button>

<!-- Modal Bootstrap -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      
        <div class="modal-header justify-content-center position-relative">
            <h1 class="modal-title fs-5 text-center w-100" id="staticBackdropLabel">Faire un dépotage</h1>
            <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        
      <div class="modal-body">
        <form action="" method="post">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nom_chauffeur" class="form-label">Nom Complet du chauffeur *</label>
                    <input type="text" class="form-control" id="nom_chauffeur" name="nom_chauffeur" placeholder="Nom et prénom" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="immatriculation_vehicule" class="form-label">Immatriculation du Véhicule *</label>
                    <input type="text" class="form-control" id="immatriculation_vehicule" name="immatriculation_vehicule" placeholder="Ex: 1234AA12" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="produit" class="form-label">Type de Carburant *</label>
                    <select class="form-select" id="produit" name="produit" required>
                        <option value="" disabled selected>Choisir un type de carburant</option>
                        <option value="essence">Essence</option>
                        <option value="gasoil">Gasoil</option>
                        <option value="mazout">Diezel</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="quantite" class="form-label">Quantité *</label>
                    <input type="number" class="form-control" id="quantite" name="quantite" placeholder="Quantité en litres" required>
                </div>
            </div>
        </form>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
        <button type="button" class="btn btn-primary">Confirmer</button>
      </div>

    </div>
  </div>
</div>
<!-- Bootstrap JS (nécessaire pour le modal) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@endsection

<style>
    .modal-dialog {
        max-width: 800px !important;
    }
  </style>
@extends('marine.layouts.template')

@section('title', 'Historique des Transactions Carburant')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Transactions Carburant</h3>
                <p class="text-subtitle text-muted">Historique des distributions de carburant.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="">Tableau de Bord</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Carburant</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if($errors->any() && old('form_type') === 'create_carburant')
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Erreurs de validation :</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<section class="section">
    <div class="card">
        <div class="card-header">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCarburantModal">
                <i class="bi bi-fuel-pump"></i> Nouvelle Transaction
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="tableCarburants">
                    <thead>
                        <tr>
                            <th>Date/Heure</th>
                            <th>Type Carburant</th>
                            <th>Quantité (L)</th>
                            <th>Personnel</th>
                            <th>Distributeur</th>
                            <th>Service (Distr.)</th>
                            <th>Véhicule Receveur</th>
                            <th>Km Receveur</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($carburants as $transaction)
                            <tr>
                                <td>{{ $transaction->date_transaction->format('d/m/Y H:i') }}</td>
                                <td>{{ $transaction->type_carburant }}</td>
                                <td>{{ number_format($transaction->quantite, 2, ',', ' ') }}</td>
                                <td>{{ $transaction->personnel->nom_complet ?? 'N/A' }}</td>
                                <td>{{ $transaction->distributeur->identifiant ?? 'N/A' }}</td>
                                <td>{{ $transaction->distributeur->service->nom ?? 'N/A' }}</td>
                                <td>{{ $transaction->vehicule_receveur_immat ?? 'N/A' }}</td>
                                <td>{{ $transaction->kilometrage_receveur ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <a href="{{-- route('corps.carburants.edit', $transaction->id) --}}#" class="btn btn-sm btn-info" title="Modifier">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    {{-- La suppression de transaction est sensible, à implémenter avec précaution --}}
                                    {{-- <button type="button" class="btn btn-sm btn-danger" title="Supprimer">...</button> --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">Aucune transaction de carburant enregistrée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $carburants->links() }}
            </div>
        </div>
    </div>
</section>

{{-- Modale de Création de Transaction Carburant --}}
<div class="modal fade" id="createCarburantModal" tabindex="-1" aria-labelledby="createCarburantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> {{-- modal-xl pour plus d'espace --}}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCarburantModalLabel">Nouvelle Transaction Carburant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('corps.carburants.store') }}" method="POST">
                @csrf
                <input type="hidden" name="form_type" value="create_carburant">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="date_transaction" class="form-label">Date et Heure <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('date_transaction') is-invalid @enderror"
                                       id="date_transaction" name="date_transaction" value="{{ old('date_transaction', now()->format('Y-m-d\TH:i')) }}" required>
                                @error('date_transaction') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="type_carburant" class="form-label">Type de Carburant <span class="text-danger">*</span></label>
                                <select class="form-select @error('type_carburant') is-invalid @enderror" id="type_carburant" name="type_carburant" required>
                                    <option value="">-- Sélectionner --</option>
                                    <option value="Diesel" {{ old('type_carburant') == 'Diesel' ? 'selected' : '' }}>Diesel</option>
                                    <option value="Essence SP95" {{ old('type_carburant') == 'Essence SP95' ? 'selected' : '' }}>Essence SP95</option>
                                    <option value="Essence SP98" {{ old('type_carburant') == 'Essence SP98' ? 'selected' : '' }}>Essence SP98</option>
                                    {{-- Ajoutez d'autres types si nécessaire --}}
                                </select>
                                @error('type_carburant') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="quantite" class="form-label">Quantité (Litres) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('quantite') is-invalid @enderror"
                                       id="quantite" name="quantite" value="{{ old('quantite') }}" placeholder="Ex: 50.75" required>
                                @error('quantite') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="personnel_id" class="form-label">Personnel ayant effectué <span class="text-danger">*</span></label>
                                <select class="form-select @error('personnel_id') is-invalid @enderror" id="personnel_id" name="personnel_id" required>
                                    <option value="">-- Sélectionner un employé --</option>
                                    @foreach ($personnels as $personnel)
                                        <option value="{{ $personnel->id }}" {{ old('personnel_id') == $personnel->id ? 'selected' : '' }}>
                                            {{ $personnel->nom_complet }} ({{ $personnel->matricule }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('personnel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="distributeur_id" class="form-label">Distributeur Utilisé <span class="text-danger">*</span></label>
                                <select class="form-select @error('distributeur_id') is-invalid @enderror" id="distributeur_id" name="distributeur_id" required>
                                    <option value="">-- Sélectionner un distributeur --</option>
                                    @foreach ($distributeurs as $distributeur)
                                        <option value="{{ $distributeur->id }}" {{ old('distributeur_id') == $distributeur->id ? 'selected' : '' }}>
                                            {{ $distributeur->identifiant }} ({{ $distributeur->service->nom ?? '' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('distributeur_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                     <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="vehicule_receveur_immat" class="form-label">Immatriculation Véhicule Receveur</label>
                                <input type="text" class="form-control @error('vehicule_receveur_immat') is-invalid @enderror"
                                       id="vehicule_receveur_immat" name="vehicule_receveur_immat" value="{{ old('vehicule_receveur_immat') }}" placeholder="Ex: AB-123-CD">
                                @error('vehicule_receveur_immat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kilometrage_receveur" class="form-label">Kilométrage Véhicule Receveur</label>
                                <input type="number" class="form-control @error('kilometrage_receveur') is-invalid @enderror"
                                       id="kilometrage_receveur" name="kilometrage_receveur" value="{{ old('kilometrage_receveur') }}" placeholder="Ex: 125000">
                                @error('kilometrage_receveur') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (Optionnel)</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer Transaction</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    {{-- SweetAlert pour la suppression (si tu l'implémentes pour les transactions) --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
    <script>
        // function confirmDeleteTransaction(transactionId) { ... }

        @if($errors->any() && old('form_type') === 'create_carburant')
            document.addEventListener('DOMContentLoaded', function() {
                var createModal = new bootstrap.Modal(document.getElementById('createCarburantModal'));
                createModal.show();
            });
        @endif
    </script>
@endsection
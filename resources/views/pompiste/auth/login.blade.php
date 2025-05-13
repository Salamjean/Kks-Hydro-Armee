<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Soute</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- Important pour AJAX POST --}}
    <link rel="stylesheet" href="{{ asset('assetsSEA/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assetsSEA/css/pages/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        #souteSelectionContainer { margin-top: 1rem; }
    </style>
</head>
<body>

    <main class="main-content mt-0">
        <div class="auth-wrapper" style="background-image: url('{{ url('assets/images/auth/soute.png') }}');
                                    background-size: contain; background-repeat: no-repeat; background-position: 60% center;">
            <div class="auth-card">
                <h1 class="auth-title">Connexion Espace Soute</h1>
                <p class="auth-subtitle mb-5">Connectez-vous.</p>

                @if (session('status'))
                    <div class="alert alert-success" role="alert">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('soute.dashboard.handleLogin') }}" id="loginSouteForm">
                    @csrf
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="text" class="form-control form-control-xl @error('email_or_matricule') is-invalid @enderror"
                            id="email_or_matricule" name="email_or_matricule" value="{{ old('email_or_matricule') }}"
                            placeholder="Votre Email ou Matricule Employé" required>
                        <div class="form-control-icon"><i class="bi bi-person"></i></div>
                    </div>

                    {{-- Ce champ sera rempli par AJAX ou par l'utilisateur si plusieurs soutes --}}
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="text" class="form-control form-control-xl @error('matricule_soute') is-invalid @enderror"
                            id="matricule_soute_display" name="matricule_soute" value="{{ old('matricule_soute') }}"
                            placeholder="Matricule de la Soute" required readonly> {{-- readonly initialement --}}
                        <div class="form-control-icon"><i class="bi bi-hdd-stack"></i></div>
                    </div>

                    {{-- Conteneur pour la sélection de soute si multiple --}}
                    <div id="souteSelectionContainer" class="mb-4" style="display:none;">
                        <label for="soute_id_selected" class="form-label">Vous êtes assigné(e) à plusieurs soutes. Veuillez en sélectionner une :</label>
                        <select name="soute_id_selected" id="soute_id_selected" class="form-select form-control-xl">
                            {{-- Les options seront ajoutées par JavaScript --}}
                        </select>
                    </div>
                    <input type="hidden" name="multiple_soutes_found" id="multiple_soutes_found" value="false">


                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="password" class="form-control form-control-xl @error('password') is-invalid @enderror"
                            name="password" placeholder="Mot de passe (laisser vide si 1ère connexion)">
                        <div class="form-control-icon"><i class="bi bi-shield-lock"></i></div>
                    </div>
                    <div class="d-flex justify-content-center mt-5">
                        <button type="submit" class="btn btn-primary btn-lg shadow-lg">Se connecter</button>
                    </div>                    
                </form>
            </div>
        </div>
    </main>



</body>
</html>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const emailOrMatriculeInput = document.getElementById('email_or_matricule');
        const matriculeSouteDisplay = document.getElementById('matricule_soute_display');
        const souteSelectionContainer = document.getElementById('souteSelectionContainer');
        const souteSelect = document.getElementById('soute_id_selected');
        const multipleSoutesFoundInput = document.getElementById('multiple_soutes_found');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        let debounceTimer;
        emailOrMatriculeInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const value = this.value.trim();
                if (value.length > 3) { // Déclenche après quelques caractères
                    fetch("{{ route('soute.dashboard.getPersonnelSouteInfo') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken // Envoi du token
        },
        body: JSON.stringify({ email_or_matricule: value })
                    })
                    .then(response => response.json())
                    .then(data => {
                        matriculeSouteDisplay.readOnly = true; // Garder readonly
                        souteSelectionContainer.style.display = 'none';
                        souteSelect.innerHTML = ''; // Vider les anciennes options
                        multipleSoutesFoundInput.value = 'false';

                        if (data.success) {
                            if (data.multiple_soutes) {
                                matriculeSouteDisplay.value = ''; // Vider si plusieurs
                                souteSelectionContainer.style.display = 'block';
                                multipleSoutesFoundInput.value = 'true';
                                data.soutes.forEach(soute => {
                                    const option = document.createElement('option');
                                    option.value = soute.id; // On enverra l'ID
                                    option.textContent = soute.nom + ' (' + soute.matricule_soute + ')';
                                    souteSelect.appendChild(option);
                                });
                                // Sélectionner la première soute par défaut et mettre son matricule dans le display
                                if (data.soutes.length > 0) {
                                    souteSelect.value = data.soutes[0].id;
                                    matriculeSouteDisplay.value = data.soutes[0].matricule_soute; // Afficher le matricule de la soute sélectionnée
                                }
                                souteSelect.addEventListener('change', function() {
                                    const selectedMatricule = data.soutes.find(s => s.id == this.value)?.matricule_soute || '';
                                    matriculeSouteDisplay.value = selectedMatricule;
                                });

                            } else if (data.soutes && data.soutes.length === 1) {
                                matriculeSouteDisplay.value = data.soutes[0].matricule_soute;
                                // Optionnel: créer un input caché pour soute_id_selected avec cette valeur
                                // pour uniformiser le traitement côté serveur.
                                // Ou ajuster le contrôleur pour prendre matricule_soute si soute_id_selected n'est pas là.
                            } else {
                                matriculeSouteDisplay.value = 'Aucune soute trouvée';
                            }
                        } else {
                            matriculeSouteDisplay.value = ''; // ou data.message
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching soute info:', error);
                        matriculeSouteDisplay.value = 'Erreur de communication';
                    });
                } else {
                    matriculeSouteDisplay.value = '';
                    souteSelectionContainer.style.display = 'none';
                    souteSelect.innerHTML = '';
                    multipleSoutesFoundInput.value = 'false';
                }
            }, 750); // Délai de debounce en ms
        });
    });
</script>
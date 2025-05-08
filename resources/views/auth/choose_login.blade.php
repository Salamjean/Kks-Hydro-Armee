<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue - Sélectionner Connexion</title>
    {{-- Ajoute ici tes CSS de base si nécessaire, ex: Bootstrap --}}
    <link rel="stylesheet" href="{{ asset('assetsSEA/css/bootstrap.css') }}">
    <style>
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f4f7f6; }
        .login-container { text-align: center; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        .login-container h1 { margin-bottom: 30px; }
        .login-container .btn { margin: 10px; padding: 10px 20px; font-size: 1.1em; }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Bienvenue</h1>
        <p>Veuillez sélectionner votre type de connexion :</p>
        <div>
            <a href="{{ route('admin.login') }}" class="btn btn-primary">Connexion Administrateur (SEA)</a>
        </div>
        <div>
            <a href="{{ route('corps.login') }}" class="btn btn-success">Connexion Corps d'Armée</a>
        </div>
        {{-- Tu pourrais aussi ajouter un lien vers la connexion Super Admin si pertinent --}}
        {{-- <div>
            <a href="{{ route('superadmin.login') }}" class="btn btn-secondary">Connexion Super Administrateur</a>
        </div> --}}
    </div>
</body>
</html>
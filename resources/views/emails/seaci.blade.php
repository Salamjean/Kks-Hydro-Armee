<!DOCTYPE html>
<html>
<head>
    <title>SEA - Confirmation d'enregistrement</title>
</head>
<body>
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <img src="{{ $logoUrl }}" alt="Logo OMAR" width="150">
            </td>
        </tr>
        <tr>
            <td>
                <h1>Création du compte du service d'essence des armées.</h1>
                <p>Le compte du service des essences des armées a été créer avec succès.</p>
                <p>Cliquez sur le bouton ci-dessous pour valider votre compte.</p>
                <p>Saisissez le code <strong>{{ $code }}</strong> dans le formulaire qui apparaîtra.</p>
                <p><a href="{{ url('/validate-sea-account/' . $email) }}" style="background-color:black; border: none; color: white; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; cursor: pointer;">Valider mon compte</a></p>
                <p>Merci pour le service.</p>
            </td>
        </tr>
    </table>
</body>
</html>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $titre ?? 'Rapport des Statistiques' }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif; /* Supporte les caractères UTF-8 */
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 20px;
        }
        h1, h4 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            text-align: right;
            font-size: 10px;
            color: #777;
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            height: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>{{ $titre ?? 'Rapport des Statistiques Mensuelles' }}</h1>
        <p style="text-align: right;">Exporté le: {{ $dateExport ?? now()->format('d/m/Y H:i') }}</p>

        <h4>Statistiques de Distribution et Dépotage</h4>
        <table>
            <thead>
                <tr>
                    <th>Mois</th>
                    <th>Distribution (L)</th>
                    <th>Dépotage (L)</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($statistiques) && $statistiques->count() > 0)
                    @foreach($statistiques as $statistique)
                        <tr>
                            <td>{{ $statistique->mois }}</td>
                            <td>{{ $statistique->distribution }} L</td>
                            <td>{{ $statistique->depotage }} L</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" style="text-align: center;">Aucune donnée disponible.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="footer">
        Document généré par l'application
    </div>
</body>
</html>
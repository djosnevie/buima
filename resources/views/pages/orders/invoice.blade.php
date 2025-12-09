<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture #{{ $commande->numero_commande }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            margin: 0;
            padding: 0;
            background-color: #fff;
            color: #000;
            display: flex;
            justify-content: center;
        }

        .ticket {
            width: 300px;
            /* Standard thermal printer width */
            margin: 0 auto;
            padding: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header h2 {
            margin: 0 0 5px 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .header p {
            margin: 2px 0;
            font-size: 11px;
        }

        .invoice-content {
            padding: 0 100px;
        }

        .info {
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
        }

        .info div {
            margin-bottom: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th {
            text-align: left;
            border-bottom: 1px dashed #000;
            padding: 5px 0;
            font-size: 11px;
        }

        td {
            padding: 5px 0;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .totals {
            border-top: 1px dashed #000;
            padding-top: 5px;
            text-align: right;
        }

        .totals div {
            margin-bottom: 3px;
        }

        .grand-total {
            font-weight: bold;
            font-size: 16px;
            margin-top: 5px;
            border-top: 1px solid #000;
            /* Double line effect or solid */
            padding-top: 5px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 11px;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }

        @media print {
            body {
                width: 100%;
                background-color: #fff;
            }

            .ticket {
                width: 100%;
                margin: 0;
                padding: 0;
            }

            @page {
                margin: 0;
            }
        }
    </style>
</head>

<body>
    @php
        $etablissement = auth()->user()->etablissement ?? $commande->etablissement;
    @endphp
    <div class="ticket">
        <div class="header">
            @if($etablissement && $etablissement->logo)
                <img src="{{ asset('storage/' . $etablissement->logo) }}" alt="Logo"
                    style="max-width: 80px; max-height: 80px; margin-bottom: 10px; border-radius: 50%;">
            @endif

            <h2 style="{{ $etablissement && $etablissement->logo ? 'margin-top: 5px;' : '' }}">
                {{ $etablissement->nom ?? "O'Menu" }}
            </h2>

            <p>{{ $etablissement->adresse ?? 'Adresse non configurée' }}</p>
            <p>Tél: {{ $etablissement->telephone ?? 'Non spécifié' }}</p>
        </div>

        <div class="invoice-content">
            <div class="info">
                <div>Date: {{ $commande->created_at->format('d/m/Y H:i') }}</div>
                <div>Commande: #{{ substr($commande->numero_commande, -4) }}</div>
                <div>Serveur: {{ $commande->user->name }}</div>
                @if($commande->client_nom)
                    <div>Client: {{ $commande->client_nom }}</div>
                @endif
                @if($commande->client_telephone)
                    <div>Tél Client: {{ $commande->client_telephone }}</div>
                @endif
                @if($commande->table)
                    <div>Table: {{ $commande->table->numero }}</div>
                @endif
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 15%">Qté</th>
                        <th style="width: 55%">Article</th>
                        <th class="text-right" style="width: 30%">Prix</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($commande->items as $item)
                        <tr>
                            <td>{{ $item->quantite }}x</td>
                            <td>{{ $item->produit->nom }}</td>
                            <td class="text-right">{{ number_format($item->sous_total, 0, ',', ' ') }}
                                {{ $etablissement->devise ?? 'XAF' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="totals">
                <div>Sous-total: {{ number_format($commande->sous_total, 0, ',', ' ') }}
                    {{ $etablissement->devise ?? 'XAF' }}
                </div>
                <div>TVA (10%): {{ number_format($commande->montant_taxes, 0, ',', ' ') }}
                    {{ $etablissement->devise ?? 'XAF' }}
                </div>
                <div class="grand-total">
                    TOTAL: {{ number_format($commande->total, 0, ',', ' ') }}
                    {{ $etablissement->devise ?? 'XAF' }}
                </div>
            </div>

            <div class="footer">
                <p>Merci de votre visite !</p>
                <p>A bientôt</p>
            </div>
        </div>
    </div>

    <script>
        window.onload = function () {
            window.print();
        }
    </script>
</body>

</html>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Facture #{{ $commande->numero_commande }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            max-width: 300px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }

        .logo {
            font-weight: bold;
            font-size: 20px;
            margin-bottom: 5px;
        }

        .info {
            margin-bottom: 10px;
        }

        .items {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .items th {
            text-align: left;
            border-bottom: 1px solid #000;
        }

        .items td {
            padding: 5px 0;
        }

        .totals {
            text-align: right;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
        }

        @media print {
            @page {
                margin: 0;
            }

            body {
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="logo">OMENU</div>
        <div>Restaurant & Lounge</div>
        <div>Tél: 067737037</div>
    </div>

    <div class="info">
        <div>Date: {{ $commande->created_at->format('d/m/Y H:i') }}</div>
        <div>Commande: #{{ substr($commande->numero_commande, -4) }}</div>
        <div>Serveur: {{ $commande->user->name }}</div>
        @if($commande->table)
            <div>Table: {{ $commande->table->numero }}</div>
        @endif
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>Qté</th>
                <th>Article</th>
                <th style="text-align: right">Prix</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commande->items as $item)
                <tr>
                    <td>{{ $item->quantite }}x</td>
                    <td>{{ $item->produit->nom }}</td>
                    <td style="text-align: right">{{ number_format($item->sous_total, 2) }}CFA</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div>Sous-total: {{ number_format($commande->sous_total, 2) }}CFA</div>
        <div>TVA (10%): {{ number_format($commande->montant_taxes, 2) }}CFA</div>
        <div style="font-weight: bold; font-size: 16px; margin-top: 5px">
            TOTAL: {{ number_format($commande->total, 2) }}CFA
        </div>
    </div>

    <div class="footer">
        <div>Merci de votre visite !</div>
        <div>A bientôt</div>
    </div>

    <script>
        window.onload = function () {
            window.print();
        }
    </script>
</body>

</html>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Vente journalière</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
            max-width: 350px;
            margin: 0 auto;
            padding: 8px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h3 {
            margin: 5px 0;
            font-size: 20px;
            font-weight: bold;
        }

        .date-range {
            display: flex;
            justify-content: center;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .date-range div {
            border: 1px solid #000;
            padding: 3px 8px;
            margin: 0 5px;
        }

        .info {
            font-size: 15px;
            margin-bottom: 15px;
            text-align: center;
        }

        .summary-totals {
            margin-bottom: 20px;
            font-size: 16px;
            font-weight: bold;
        }

        .summary-totals .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .summary-totals .total-row {
            border-top: 1px dashed #000;
            padding-top: 5px;
            margin-top: 5px;
        }

        .servers-table, .financial-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
            margin-bottom: 20px;
        }

        .servers-table th, .servers-table td {
            border: 1px solid #000;
            padding: 5px;
        }

        .servers-table th {
            text-align: left;
            font-weight: normal;
        }
        
        .servers-table td.amount {
            text-align: right;
            font-weight: normal;
        }

        .financial-table td {
            padding: 4px 0;
            font-weight: bold;
        }

        .financial-table td.amount {
            text-align: right;
        }

        .exchange-rate {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            font-size: 14px;
            font-weight: bold;
        }

        .exchange-rate td {
            border: 1px solid #000;
            padding: 5px;
        }

        .text-center {
            text-align: center;
        }

        @media print {
            @page {
                margin: 0;
            }
            body {
                padding: 10px 5px;
            }
        }
    </style>
</head>

<body>
    @php
        $devise = auth()->user()->etablissement->devise_display ?? 'FC';
        $fmt = fn($v) => number_format((float)$v, 0, ',', ' ') . ' ' . $devise;
    @endphp

    <div class="header">
        <div style="font-size: 12px; text-align: right; margin-bottom: 5px;">
            Édité le {{ now()->format('d/m/Y H:i:s') }}
        </div>
        <h3>Vente journalière</h3>
        <div style="font-size: 12px; margin-top: 6px; text-align: left;">
            <div><strong>Ouverture :</strong> {{ $session->date_ouverture->format('d/m/Y H:i') }}</div>
            <div><strong>Fermeture :</strong> {{ $session->date_fermeture ? $session->date_fermeture->format('d/m/Y H:i') : 'En cours' }}</div>
        </div>

        <div class="info">
            Caissière : <strong>{{ strtoupper($session->user->name) }}</strong>
        </div>
    </div>

    <!-- Totaux par catégorie -->
    <div class="summary-totals">
        <div class="row">
            <span>Montant Boisson</span>
            <span>{{ $fmt($boissonSales) }}</span>
        </div>
        <div class="row">
            <span>Montant Foods</span>
            <span>{{ $fmt($foodSales) }}</span>
        </div>
        <div class="row total-row">
            <span>Total {{ $devise }}</span>
            <span>{{ $fmt($caTotal) }}</span>
        </div>
    </div>

    <!-- Serveurs -->
    <table class="servers-table">
        <thead>
            <tr>
                <th>Nom de Serveur</th>
                <th style="text-align: right;">Montant Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($serversSales as $serveur)
                <tr>
                    <td>{{ strtoupper($serveur->name) }}</td>
                    <td class="amount">{{ $fmt($serveur->total) }}</td>
                </tr>
            @endforeach
            <tr>
                <td style="border: none;"></td>
                <td style="border: 1px solid #000; text-align: right; font-weight: bold;">
                    {{ $fmt($caTotal) }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Financier -->
    <table class="financial-table">
        <tr>
            <td>CA</td>
            <td style="text-align: center;">:</td>
            <td class="amount">{{ $fmt($caTotal) }}</td>
        </tr>
        <tr>
            <td>Fond</td>
            <td style="text-align: center;">:</td>
            <td class="amount">{{ $fmt($session->montant_ouverture) }}</td>
        </tr>
        <tr>
            <td>Dette Payée</td>
            <td style="text-align: center;">:</td>
            <td class="amount">{{ $fmt(0) }}</td>
        </tr>
        <tr>
            <td>Montant Carte</td>
            <td style="text-align: center;">:</td>
            <td class="amount">{{ $fmt($montantCarte) }}</td>
        </tr>
        <tr>
            <td>Dette journalière</td>
            <td style="text-align: center;">:</td>
            <td class="amount">{{ $fmt(0) }}</td>
        </tr>
        <tr>
            <td>Dépense Journalière</td>
            <td style="text-align: center;">:</td>
            <td class="amount">{{ $fmt($depenseJournaliere) }}</td>
        </tr>
        <tr>
            <td colspan="3" style="border-top: 1px dashed #000; padding-top: 5px; margin-top: 5px;"></td>
        </tr>
        <tr>
            <td>Total =</td>
            <td></td>
            <td class="amount">{{ $fmt($caTotal) }}</td>
        </tr>
    </table>

    <!-- Taux de change / Devises -->
    <table class="exchange-rate">
        <tr>
            <td style="width: 40%;">USD =</td>
            <td style="width: 60%;">&nbsp;</td>
        </tr>
        <tr>
            <td>FC =</td>
            <td>&nbsp;</td>
        </tr>
    </table>

    <script>
        window.onload = function () {
            window.print();
        }
        window.onafterprint = function () {
            window.close();
        }
    </script>
</body>

</html>

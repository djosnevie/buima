<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Vente journalière BOISSONS</title>
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
            margin-bottom: 20px;
        }

        .header h3 {
            margin: 5px 0;
            font-size: 18px;
            font-weight: bold;
        }

        .info {
            font-size: 14px;
            margin-bottom: 20px;
            text-align: center;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 3px 4px;
        }

        .items-table th {
            text-align: center;
            font-weight: bold;
        }

        .items-table th.col-qte,
        .items-table td.col-qte {
            width: 28px;
            text-align: center;
        }

        .items-table th.col-pu,
        .items-table td.col-pu,
        .items-table th.col-ttc,
        .items-table td.col-ttc {
            width: 72px;
            text-align: right;
            white-space: nowrap;
        }

        .items-table td.designation {
            text-align: left;
        }

        .total-row td {
            font-weight: bold;
            border-top: 2px solid #000;
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
        $fmt = fn($v) => number_format((float)$v, 0, ',', ' ');
    @endphp

    <div class="header">
        <div style="font-size: 12px; text-align: right; margin-bottom: 5px;">
            Édité le {{ now()->format('d/m/Y H:i:s') }}
        </div>
        <h3>Vente journalière BOISSONS</h3>
        <div style="font-size: 12px; margin-top: 6px; text-align: left;">
            <div><strong>Ouverture :</strong> {{ $session->date_ouverture->format('d/m/Y H:i') }}</div>
            <div><strong>Fermeture :</strong> {{ $session->date_fermeture ? $session->date_fermeture->format('d/m/Y H:i') : 'En cours' }}</div>
        </div>
    </div>

    <div class="info">
        Caissière : <strong>{{ strtoupper($session->user->name) }}</strong>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th class="col-qte">Qte</th>
                <th>Désignation</th>
                <th class="col-pu">PU</th>
                <th class="col-ttc">TTC</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td class="col-qte">{{ number_format($item->qte, 0, ',', '') }}</td>
                    <td class="designation">{{ $item->designation }}</td>
                    <td class="col-pu">{{ $fmt($item->pu) }}</td>
                    <td class="col-ttc">{{ $fmt($item->ttc) }} {{ $devise }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3" style="text-align: right; border-right: none;"></td>
                <td class="col-ttc" style="border-left: none;">{{ $fmt($total) }} {{ $devise }}</td>
            </tr>
        </tbody>
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

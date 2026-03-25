<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Vente journalière BOISSONS</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 15px;
            max-width: 300px;
            margin: 0 auto;
            padding: 10px;
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
            font-size: 14px;
            margin-bottom: 20px;
        }

        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 4px;
        }

        .items-table th {
            text-align: center;
            font-weight: bold;
            background-color: #f8f9fa;
        }

        .items-table td {
            text-align: right;
        }

        .items-table td.designation {
            text-align: left;
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
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
        $devise = auth()->user()->etablissement->devise_display ?? '$';
        $fmt = fn($v) => number_format((float)$v, 2, ',', ' ');
    @endphp

    <div class="header">
        <div style="font-size: 12px; text-align: right; margin-bottom: 10px;">
            {{ now()->format('d/m/Y') }}<br>
            {{ now()->format('H:i:s') }}
        </div>
        <h3>Vente journalière BOISSONS</h3>
    </div>

    <div class="info">
        Caissière : <strong>{{ strtoupper($session->user->name) }}</strong>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Qte</th>
                <th>Désignation</th>
                <th>PU</th>
                <th>TTC</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td style="text-align: center;">{{ number_format($item->qte, 0, ',', '') }}</td>
                    <td class="designation">{{ $item->designation }}</td>
                    <td>{{ $fmt($item->pu) }}{{ $devise == '$' ? '' : ',' }}</td>
                    <td>{{ $fmt($item->ttc) }} {{ $devise }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3" style="text-align: right; border-right: none;"></td>
                <td style="border-left: none;">{{ $fmt($total) }} {{ $devise }}</td>
            </tr>
        </tbody>
    </table>

    <script>
        window.onload = function () {
            window.print();
        }
    </script>
</body>

</html>

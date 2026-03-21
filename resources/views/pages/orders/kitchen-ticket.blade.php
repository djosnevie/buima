<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Bon Préparation #{{ $commande->numero_commande }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 16px;
            max-width: 300px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px dashed #000;
            padding-bottom: 15px;
        }

        .title {
            font-weight: bold;
            font-size: 24px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .info {
            margin-bottom: 20px;
            font-size: 18px;
            line-height: 1.4;
        }

        .order-type {
            font-weight: bold;
            font-size: 20px;
            padding: 5px;
            border: 2px solid #000;
            text-align: center;
            margin-top: 10px;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .items {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .items th {
            text-align: left;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            font-size: 18px;
        }

        .items td {
            padding: 10px 0;
            border-bottom: 1px dashed #ccc;
            font-size: 18px;
            font-weight: bold;
        }

        .qty {
            width: 40px;
            font-size: 20px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            border-top: 2px dashed #000;
            padding-top: 10px;
            font-size: 14px;
        }

        .notes-section {
            margin-top: 20px;
            border: 1px solid #000;
            padding: 10px;
        }

        .notes-title {
            font-weight: bold;
            margin-bottom: 5px;
            text-decoration: underline;
        }

        @media print {
            .page-break {
                page-break-after: always;
            }

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
        $etablissement = auth()->user()->etablissement ?? $commande->etablissement;
        
        $foodItems = $commande->items->filter(fn($item) => $item->produit->type !== 'boisson');
        $drinkItems = $commande->items->filter(fn($item) => $item->produit->type === 'boisson');
        
        $tickets = [];
        if ($foodItems->count() > 0) {
            $tickets[] = ['title' => 'CUISINE', 'items' => $foodItems];
        }
        if ($drinkItems->count() > 0) {
            $tickets[] = ['title' => 'BAR', 'items' => $drinkItems];
        }
    @endphp

    @foreach($tickets as $index => $ticket)
        <div class="ticket-section">
            <div class="header">
                <div class="title">BON DE PRÉPARATION</div>
                <div style="font-size: 22px; font-weight: bold; text-decoration: underline; margin-bottom: 5px;">{{ $ticket['title'] }}</div>
                <div style="font-size: 14px;">{{ $etablissement->nom ?? 'OMENU' }}</div>
            </div>

            <div class="info">
                <div><strong>Date:</strong> {{ $commande->created_at->format('d/m/Y H:i') }}</div>
                <div><strong>Commande:</strong> #{{ substr($commande->numero_commande, -4) }}</div>
                <div><strong>Serveur:</strong> {{ $commande->user->name }}</div>
            </div>

            <div class="order-type">
                {{ str_replace('_', ' ', $commande->type_commande) }}
                
                @if($commande->type_commande === 'sur_place' && $commande->table)
                    <br>
                    TABLE : {{ $commande->table->numero }}
                @endif
            </div>

            @if($commande->client_nom && in_array($commande->type_commande, ['emporter', 'livraison']))
                <div style="margin-bottom: 15px; font-weight: bold; font-size: 18px;">
                    Client : {{ $commande->client_nom }}
                </div>
            @endif

            <table class="items">
                <thead>
                    <tr>
                        <th class="qty">Qté</th>
                        <th>Article</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ticket['items'] as $item)
                        <tr>
                            <td class="qty">{{ $item->quantite }}x</td>
                            <td>{{ $item->produit->nom }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($commande->notes)
                <div class="notes-section">
                    <div class="notes-title">NOTES / INSTRUCTIONS :</div>
                    <div style="font-size: 16px; font-weight: bold;">{{ $commande->notes }}</div>
                </div>
            @endif

            <div class="footer">
                <div>Bon édité le {{ now()->format('d/m/Y à H:i') }}</div>
                <div style="margin-top: 10px;">_ _ _ _ _ _ _ _ _ _ _ _ _ _</div>
            </div>
        </div>

        @if(!$loop->last)
            <div class="page-break" style="border-bottom: 2px dashed #000; margin: 40px 0;"></div>
        @endif
    @endforeach

    <script>
        window.onload = function () {
            window.print();
        }
    </script>
</body>

</html>

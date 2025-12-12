<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #bf3a29; padding-bottom: 20px; }
        .logo { max-height: 60px; margin-bottom: 10px; }
        .title { font-size: 24px; font-weight: bold; color: #bf3a29; margin: 0; }
        .meta { margin-top: 5px; color: #666; font-size: 11px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f3f4f6; color: #bf3a29; font-weight: bold; padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        .text-right { text-align: right; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; padding: 10px; text-align: center; border-top: 1px solid #eee; font-size: 10px; color: #999; }
        .summary-box { background: #f9fafb; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #eee; }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .total-row { font-weight: bold; background-color: #fff1f2; }
    </style>
</head>
<body>
    <div class="header">
        @if($etablissement->logo)
            <img src="{{ public_path('storage/' . $etablissement->logo) }}" class="logo">
        @else
             <h2 style="color: #bf3a29; margin: 0;">{{ $etablissement->nom }}</h2>
        @endif
        <h1 class="title">{{ $title }}</h1>
        <div class="meta">
            Période: {{ $start }} - {{ $end }} | Généré le: {{ $generated_at }}
        </div>
    </div>

    @if($type === 'sales')
        <div class="summary-box">
             <h3>Résumé des Ventes</h3>
             <p>Total des revenus: <strong>{{ number_format($total_revenue, 0, ',', ' ') }} {{ $etablissement->devise }}</strong></p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th class="text-right">Nombre de Commandes</th>
                    <th class="text-right">Total Revenu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($daily_sales as $day)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($day->date)->format('d/m/Y') }}</td>
                        <td class="text-right">{{ $day->count }}</td>
                        <td class="text-right">{{ number_format($day->total, 0, ',', ' ') }} {{ $etablissement->devise }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @elseif($type === 'products')
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th class="text-right">Quantité Vendue</th>
                    <th class="text-right">Revenu Généré</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                    <tr>
                        <td>{{ $product->nom }}</td>
                        <td class="text-right">{{ $product->qty }}</td>
                        <td class="text-right">{{ number_format($product->revenue, 0, ',', ' ') }} {{ $etablissement->devise }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @elseif($type === 'categories')
        <table>
            <thead>
                <tr>
                    <th>Catégorie</th>
                    <th class="text-right">Articles Vendus</th>
                    <th class="text-right">Revenu Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $cat)
                    <tr>
                        <td>{{ $cat->nom }}</td>
                        <td class="text-right">{{ $cat->count }}</td>
                        <td class="text-right">{{ number_format($cat->revenue, 0, ',', ' ') }} {{ $etablissement->devise }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @elseif($type === 'payment')
        <table>
             <thead>
                <tr>
                    <th>Statut / Méthode</th>
                    <th class="text-right">Nombre</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $pay)
                    <tr>
                        <td>{{ ucfirst($pay->statut) }}</td>
                        <td class="text-right">{{ $pay->count }}</td>
                        <td class="text-right">{{ number_format($pay->total, 0, ',', ' ') }} {{ $etablissement->devise }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
    @elseif($type === 'staff')
        <table>
             <thead>
                <tr>
                    <th>Employé</th>
                    <th class="text-right">Commandes Prises</th>
                    <th class="text-right">Chiffre d'Affaires</th>
                </tr>
            </thead>
            <tbody>
                @foreach($staff as $member)
                    <tr>
                        <td>{{ $member->name }}</td>
                        <td class="text-right">{{ $member->count }}</td>
                        <td class="text-right">{{ number_format($member->revenue, 0, ',', ' ') }} {{ $etablissement->devise }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Document généré par O'Menu - Système de Gestion Restaurant
    </div>
</body>
</html>

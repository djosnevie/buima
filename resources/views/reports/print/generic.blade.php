<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #bf3a29; padding-bottom: 20px; }
        .logo { max-height: 80px; margin-bottom: 10px; }
        .title { font-size: 24px; font-weight: bold; color: #bf3a29; margin: 0; }
        .meta { margin-top: 5px; color: #666; font-size: 12px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f3f4f6; color: #bf3a29; font-weight: bold; padding: 10px; text-align: left; border-bottom: 2px solid #ddd; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        .text-right { text-align: right; }
        
        .footer { 
            position: fixed; bottom: 0; left: 0; right: 0; 
            padding: 10px; text-align: center; border-top: 1px solid #eee; 
            font-size: 10px; color: #999; background: white;
        }
        
        .summary-box { background: #f9fafb; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e5e7eb; }
        
        @media print {
            .footer { position: fixed; bottom: 0; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="header">
        @if($etablissement->logo)
            <img src="{{ asset('storage/' . $etablissement->logo) }}" class="logo">
        @else
             <h2 style="color: #bf3a29; margin: 0;">{{ $etablissement->nom }}</h2>
        @endif
        <h1 class="title">{{ $title }}</h1>
        <div class="meta">
            @if($start && $end)
                Période: {{ $start }} - {{ $end }} |
            @endif
            Généré le: {{ $generated_at }}
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

    @elseif($type === 'product_list')
        <table>
             <thead>
                <tr>
                    <th>Catégorie</th>
                    <th>Produit</th>
                    <th class="text-right">Prix Actuel</th>
                </tr>
            </thead>
            <tbody>
                @foreach($product_list as $prod)
                    <tr>
                        <td>{{ $prod->categorie }}</td>
                        <td>{{ $prod->nom }}</td>
                        <td class="text-right">{{ number_format($prod->prix, 0, ',', ' ') }} {{ $etablissement->devise }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($type === 'caisse_sessions')
        <table>
            <thead>
                <tr>
                    <th>Caisse</th>
                    <th>Caissier</th>
                    <th>Ouverture</th>
                    <th class="text-right">Fermeture Réel</th>
                    <th class="text-right">Écart</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sessions as $sess)
                    <tr>
                        <td>{{ $sess->caisse_nom }}</td>
                        <td>{{ $sess->caissier }}</td>
                        <td>{{ \Carbon\Carbon::parse($sess->date_ouverture)->format('d/m H:i') }}</td>
                        <td class="text-right">{{ number_format($sess->montant_fermeture_reel, 0, ',', ' ') }}</td>
                        <td class="text-right">{{ number_format($sess->montant_fermeture_reel - $sess->montant_fermeture_theorique, 0, ',', ' ') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($type === 'stock_valuation')
        <div class="summary-box">
            <p>Valeur Totale du Stock: <strong>{{ number_format($total_valuation, 0, ',', ' ') }} {{ $etablissement->devise }}</strong></p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th class="text-right">Stock</th>
                    <th class="text-right">Prix Achat</th>
                    <th class="text-right">Valorisation</th>
                </tr>
            </thead>
            <tbody>
                @foreach($valuation as $val)
                    <tr>
                        <td>{{ $val->nom }}</td>
                        <td class="text-right">{{ $val->quantite }}</td>
                        <td class="text-right">{{ number_format($val->prix_achat, 0, ',', ' ') }}</td>
                        <td class="text-right">{{ number_format($val->total_value, 0, ',', ' ') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($type === 'expenses')
        <div class="summary-box">
            <p>Total des Dépenses: <strong>{{ number_format($total_expenses, 0, ',', ' ') }} {{ $etablissement->devise }}</strong></p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Catégorie</th>
                    <th>Description</th>
                    <th class="text-right">Montant</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenses as $exp)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($exp->date_depense)->format('d/m/Y') }}</td>
                        <td>{{ $exp->categorie_nom }}</td>
                        <td>{{ $exp->description }}</td>
                        <td class="text-right">-{{ number_format($exp->montant, 0, ',', ' ') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Document généré par O'Menu - Système de Gestion Restaurant
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>

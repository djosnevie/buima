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
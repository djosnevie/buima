<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockProduit extends Model
{
    use HasFactory;

    protected $table = 'stocks_produits';

    protected $fillable = [
        'produit_id',
        'quantite',
        'seuil_alerte',
    ];

    protected $casts = [
        'quantite' => 'integer',
        'seuil_alerte' => 'integer',
    ];

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class);
    }
}

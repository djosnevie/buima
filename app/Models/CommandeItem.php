<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommandeItem extends Model
{
    protected $fillable = [
        'commande_id',
        'produit_id',
        'quantite',
        'prix_unitaire',
        'sous_total',
        'notes',
        'statut',
        'quantite_imprimee',
    ];

    protected $casts = [
        'prix_unitaire' => 'decimal:2',
        'sous_total' => 'decimal:2',
        'quantite' => 'integer',
    ];

    public function commande(): BelongsTo
    {
        return $this->belongsTo(Commande::class);
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class)->withTrashed();
    }

    protected static function booted()
    {
        static::saving(function ($item) {
            $item->sous_total = $item->quantite * $item->prix_unitaire;
        });
    }
}

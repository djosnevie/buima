<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Produit extends Model
{
    protected $fillable = [
        'etablissement_id',
        'categorie_id',
        'nom',
        'code_barre',
        'description',
        'image',
        'prix_vente',
        'prix_achat',
        'tva',
        'type',
        'disponible',
        'gestion_stock',
    ];

    protected $casts = [
        'prix_vente' => 'decimal:2',
        'prix_achat' => 'decimal:2',
        'tva' => 'decimal:2',
        'disponible' => 'boolean',
        'gestion_stock' => 'boolean',
    ];

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('disponible', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}

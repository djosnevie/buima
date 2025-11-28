<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends Model
{
    protected $fillable = [
        'etablissement_id',
        'nom',
        'description',
        'ordre',
        'actif',
    ];

    protected $casts = [
        'ordre' => 'integer',
        'actif' => 'boolean',
    ];

    public function produits(): HasMany
    {
        return $this->hasMany(Produit::class);
    }

    public function scopeActive($query)
    {
        return $query->where('actif', true)->orderBy('ordre');
    }
}

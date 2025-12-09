<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Etablissement extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'type',
        'adresse',
        'telephone',
        'email',
        'logo',
        'devise',
        'theme_color',
        'secondary_color',
        'button_color',
        'configuration',
        'actif',
    ];

    protected $casts = [
        'configuration' => 'array',
        'actif' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Categorie::class);
    }

    public function produits(): HasMany
    {
        return $this->hasMany(Produit::class);
    }

    public function tables(): HasMany
    {
        return $this->hasMany(Table::class);
    }
}

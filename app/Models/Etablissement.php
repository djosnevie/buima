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
        'slug',
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
        'manager_id',
        'rccm',
        'nui',
        'site_web',
        'facebook',
        'instagram',
        'description',
        'tva_applicable',
        'tva_taux',
        'modules',
    ];

    protected $casts = [
        'configuration' => 'array',
        'modules' => 'array',
        'actif' => 'boolean',
        'tva_applicable' => 'boolean',
        'tva_taux' => 'decimal:2',
    ];

    public function manager(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

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

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function commandes(): HasMany
    {
        return $this->hasMany(Commande::class);
    }

    public function fournisseurs(): HasMany
    {
        return $this->hasMany(Fournisseur::class);
    }

    public function caisses(): HasMany
    {
        return $this->hasMany(Caisse::class);
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class);
    }

    public function depenses(): HasMany
    {
        return $this->hasMany(Depense::class);
    }

    public function approvisionnements(): HasMany
    {
        return $this->hasMany(Approvisionnement::class);
    }

    public function inventaires(): HasMany
    {
        return $this->hasMany(Inventaire::class);
    }

    public function getDeviseDisplayAttribute()
    {
        $map = [
            'XAF' => 'FCFA',
            'CDF' => 'FC',
            'EUR' => '€',
            'USD' => '$',
        ];

        return $map[$this->devise] ?? $this->devise;
    }

    public function hasModule(string $module): bool
    {
        // If modules is null (not yet configured), use defaults
        if (is_null($this->modules)) {
            $defaultModules = ['pos', 'qr_menu', 'orders', 'products', 'tables'];
            return in_array($module, $defaultModules);
        }

        // Otherwise respect the configured array (even if empty)
        return in_array($module, $this->modules);
    }
}

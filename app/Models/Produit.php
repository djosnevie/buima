<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Produit extends Model
{
    use SoftDeletes;

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

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class)->withTrashed();
    }

    public function stock(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(StockProduit::class);
    }

    public function mouvements(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(MouvementStock::class, 'stockable');
    }

    public function getStockActuelAttribute()
    {
        return $this->stock->quantite ?? 0;
    }

    public function getSeuilAlerteAttribute()
    {
        return $this->stock->seuil_alerte ?? 0;
    }

    public function hasSufficientStock($requestedQuantity = 1)
    {
        if (!$this->gestion_stock) {
            return true;
        }

        // Restriction: no command below the threshold
        return ($this->stock_actuel - $requestedQuantity) >= $this->seuil_alerte;
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return "https://ui-avatars.com/api/?name=" . urlencode($this->nom) . "&background=f8f9fa&color=bf3a29";
        }

        // Use the public_uploads disk specifically
        return \Illuminate\Support\Facades\Storage::disk('public_uploads')->url($this->image);
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

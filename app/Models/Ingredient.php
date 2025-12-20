<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'etablissement_id',
        'nom',
        'unite',
        'prix_achat_moyen',
        'stock_actuel',
        'seuil_alerte',
    ];

    protected $casts = [
        'prix_achat_moyen' => 'decimal:2',
        'stock_actuel' => 'decimal:2',
        'seuil_alerte' => 'decimal:2',
    ];

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function mouvements(): MorphMany
    {
        return $this->morphMany(MouvementStock::class, 'stockable');
    }
}

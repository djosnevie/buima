<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventaireItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventaire_id',
        'itemable_type',
        'itemable_id',
        'quantite_theorique',
        'quantite_reelle',
        'ecart',
        'motif',
    ];

    protected $casts = [
        'quantite_theorique' => 'decimal:2',
        'quantite_reelle' => 'decimal:2',
        'ecart' => 'decimal:2',
    ];

    public function inventaire(): BelongsTo
    {
        return $this->belongsTo(Inventaire::class);
    }

    public function itemable()
    {
        return $this->morphTo();
    }
}

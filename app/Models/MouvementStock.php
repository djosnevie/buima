<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MouvementStock extends Model
{
    use HasFactory;

    protected $table = 'mouvements_stock';

    protected $fillable = [
        'etablissement_id',
        'stockable_type',
        'stockable_id',
        'type',
        'quantite',
        'quantite_avant',
        'quantite_apres',
        'motif',
        'commentaire',
        'user_id',
        'commande_id',
        'date_mouvement',
    ];

    protected $casts = [
        'quantite' => 'integer',
        'quantite_avant' => 'integer',
        'quantite_apres' => 'integer',
        'date_mouvement' => 'date',
    ];

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stockable(): MorphTo
    {
        return $this->morphTo();
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}

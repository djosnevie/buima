<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovisionnementItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'approvisionnement_id',
        'itemable_type',
        'itemable_id',
        'quantite',
        'prix_unitaire',
        'sous_total',
    ];

    protected $casts = [
        'quantite' => 'decimal:2',
        'prix_unitaire' => 'decimal:2',
        'sous_total' => 'decimal:2',
    ];

    public function approvisionnement(): BelongsTo
    {
        return $this->belongsTo(Approvisionnement::class);
    }

    public function itemable()
    {
        return $this->morphTo();
    }
}

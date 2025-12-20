<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Approvisionnement extends Model
{
    use HasFactory;

    protected $fillable = [
        'etablissement_id',
        'fournisseur_id',
        'user_id',
        'date_approvisionnement',
        'montant_total',
        'statut',
        'notes',
    ];

    protected $casts = [
        'date_approvisionnement' => 'datetime',
        'montant_total' => 'decimal:2',
    ];

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ApprovisionnementItem::class);
    }
}

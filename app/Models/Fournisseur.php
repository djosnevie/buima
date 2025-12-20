<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fournisseur extends Model
{
    use HasFactory;

    protected $fillable = [
        'etablissement_id',
        'nom',
        'contact',
        'telephone',
        'email',
        'adresse',
    ];

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function approvisionnements(): HasMany
    {
        return $this->hasMany(Approvisionnement::class);
    }
}

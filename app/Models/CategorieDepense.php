<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategorieDepense extends Model
{
    use HasFactory;

    protected $fillable = [
        'etablissement_id',
        'nom',
        'description',
    ];

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function depenses(): HasMany
    {
        return $this->hasMany(Depense::class, 'categorie_depense_id');
    }
}

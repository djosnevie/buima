<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Depense extends Model
{
    use HasFactory;

    protected $fillable = [
        'etablissement_id',
        'user_id',
        'categorie_depense_id',
        'categorie_depense', // keeping for legacy
        'montant',
        'description',
        'date_depense',
        'reference_piece',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_depense' => 'datetime',
    ];

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categorieDepense(): BelongsTo
    {
        return $this->belongsTo(CategorieDepense::class, 'categorie_depense_id');
    }
}

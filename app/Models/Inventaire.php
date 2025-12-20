<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'etablissement_id',
        'user_id',
        'date_inventaire',
        'type_inventaire',
        'statut',
        'notes',
    ];

    protected $casts = [
        'date_inventaire' => 'datetime',
    ];

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InventaireItem::class);
    }
}

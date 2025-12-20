<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Caisse extends Model
{
    use HasFactory;

    protected $fillable = [
        'etablissement_id',
        'nom',
        'code',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(SessionCaisse::class);
    }

    public function currentSession()
    {
        return $this->sessions()->whereNull('date_fermeture')->latest()->first();
    }
}

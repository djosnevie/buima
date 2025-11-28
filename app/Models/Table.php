<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    protected $fillable = [
        'etablissement_id',
        'numero',
        'capacite',
        'zone',
        'statut',
        'qr_code',
    ];

    protected $casts = [
        'capacite' => 'integer',
    ];

    public function commandes(): HasMany
    {
        return $this->hasMany(Commande::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('statut', 'libre');
    }

    public function scopeOccupied($query)
    {
        return $query->where('statut', 'occupee');
    }

    public function markAsOccupied(): void
    {
        $this->update(['statut' => 'occupee']);
    }

    public function markAsFree(): void
    {
        $this->update(['statut' => 'libre']);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SessionCaisse extends Model
{
    use HasFactory;

    protected $table = 'sessions_caisse';

    protected $fillable = [
        'caisse_id',
        'user_id',
        'date_ouverture',
        'date_fermeture',
        'montant_ouverture',
        'montant_fermeture_reel',
        'montant_fermeture_theorique',
        'ecart',
        'notes',
        'statut',
    ];

    protected $casts = [
        'date_ouverture' => 'datetime',
        'date_fermeture' => 'datetime',
        'montant_ouverture' => 'decimal:2',
        'montant_fermeture_reel' => 'decimal:2',
        'montant_fermeture_theorique' => 'decimal:2',
        'ecart' => 'decimal:2',
    ];

    public function caisse(): BelongsTo
    {
        return $this->belongsTo(Caisse::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'session_caisse_id');
    }
}

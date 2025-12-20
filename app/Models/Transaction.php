<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'etablissement_id',
        'session_caisse_id',
        'user_id',
        'type',
        'montant',
        'mode_paiement',
        'description',
        'statut',
        'reference_id',
        'reference_type',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
    ];

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function sessionCaisse(): BelongsTo
    {
        return $this->belongsTo(SessionCaisse::class, 'session_caisse_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }
}

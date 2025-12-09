<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commande extends Model
{
    protected $fillable = [
        'etablissement_id',
        'table_id',
        'numero_commande',
        'type_commande',
        'client_nom',
        'client_telephone',
        'client_adresse',
        'user_id',
        'statut',
        'sous_total',
        'montant_reduction',
        'montant_taxes',
        'montant_livraison',
        'total',
        'date_commande',
        'heure_prise',
        'heure_preparation',
        'heure_service',
        'notes',
    ];

    protected $casts = [
        'date_commande' => 'datetime',
        'heure_prise' => 'datetime',
        'heure_preparation' => 'datetime',
        'heure_service' => 'datetime',
        'sous_total' => 'decimal:2',
        'montant_reduction' => 'decimal:2',
        'montant_taxes' => 'decimal:2',
        'montant_livraison' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

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
        return $this->hasMany(CommandeItem::class);
    }

    public function calculateTotal(): void
    {
        $this->sous_total = $this->items->sum('sous_total');
        $this->montant_taxes = $this->sous_total * 0.1; // 10% TVA
        $this->total = $this->sous_total + $this->montant_taxes + $this->montant_livraison - $this->montant_reduction;
        $this->save();
    }

    public static function generateOrderNumber(): string
    {
        return 'CMD-' . now()->format('Ymd') . '-' . str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
    }
}

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
        'caisse_id',
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

    public function caisse(): BelongsTo
    {
        return $this->belongsTo(Caisse::class);
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

        $tvaTaux = 0;
        if ($this->etablissement && $this->etablissement->tva_applicable) {
            $tvaTaux = (float) $this->etablissement->tva_taux / 100;
        }

        $this->montant_taxes = (string) round($this->sous_total * $tvaTaux, 2);
        $this->total = (string) round($this->sous_total + (float) $this->montant_taxes + $this->montant_livraison - $this->montant_reduction, 2);
        $this->save();
    }

    public function updateTotal(): void
    {
        $this->calculateTotal();
    }

    public static function generateOrderNumber(string $prefix = 'CMD'): string
    {
        $todayStr = now()->format('Ymd');
        $count = static::whereDate('created_at', today())->count() + 1;
        return $prefix . '-' . $todayStr . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}

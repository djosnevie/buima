<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // On met à jour automatiquement les types des anciens produits 
        // en se basant sur le type de leur catégorie parente.
        \Illuminate\Support\Facades\DB::statement("
            UPDATE produits p
            JOIN categories c ON p.categorie_id = c.id
            SET p.type = c.type
            WHERE p.categorie_id IS NOT NULL 
              AND c.type IS NOT NULL 
              AND p.type != c.type
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pas de rollback nécessaire pour une correction logique de données
    }
};

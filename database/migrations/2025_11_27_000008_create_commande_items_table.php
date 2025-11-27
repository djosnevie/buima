<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commande_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained()->onDelete('cascade');
            $table->foreignId('produit_id')->constrained()->onDelete('restrict');
            $table->integer('quantite');
            $table->decimal('prix_unitaire', 10, 2);
            $table->decimal('sous_total', 10, 2);
            $table->text('notes')->nullable(); // modifications, suppléments
            $table->enum('statut', ['en_attente', 'en_preparation', 'pret', 'servi'])->default('en_attente');
            $table->timestamps();
            
            $table->index(['commande_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commande_items');
    }
};
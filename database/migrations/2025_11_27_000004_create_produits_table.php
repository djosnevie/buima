<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etablissement_id')->constrained()->onDelete('cascade');
            $table->foreignId('categorie_id')->constrained()->onDelete('restrict');
            $table->string('nom');
            $table->string('code_barre')->nullable()->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('prix_vente', 10, 2);
            $table->decimal('prix_achat', 10, 2)->nullable();
            $table->decimal('tva', 5, 2)->default(0); // Pourcentage TVA
            $table->enum('type', ['plat', 'boisson', 'dessert', 'entree', 'autre']);
            $table->boolean('disponible')->default(true);
            $table->boolean('gestion_stock')->default(true); // true = gérer stock
            $table->timestamps();
            
            $table->index(['etablissement_id', 'disponible']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};
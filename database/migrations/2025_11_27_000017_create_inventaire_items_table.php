<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventaire_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventaire_id')->constrained()->onDelete('cascade');
            $table->morphs('inventoriable'); // ingredient ou produit
            $table->decimal('quantite_theorique', 10, 3);
            $table->decimal('quantite_reelle', 10, 3);
            $table->decimal('ecart', 10, 3);
            $table->decimal('valeur_ecart', 10, 2)->default(0);
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventaire_items');
    }
};
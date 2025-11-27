<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mouvements_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etablissement_id')->constrained()->onDelete('cascade');
            $table->morphs('stockable'); // ingredient_id ou produit_id
            $table->enum('type', ['entree', 'sortie', 'ajustement', 'perte', 'retour']);
            $table->decimal('quantite', 10, 3);
            $table->decimal('quantite_avant', 10, 3);
            $table->decimal('quantite_apres', 10, 3);
            $table->string('motif')->nullable();
            $table->text('commentaire')->nullable();
            $table->foreignId('user_id')->constrained(); // qui a fait le mouvement
            $table->foreignId('commande_id')->nullable()->constrained(); // si lié à commande
            $table->date('date_mouvement');
            $table->timestamps();
            
            $table->index(['etablissement_id', 'date_mouvement']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mouvements_stock');
    }
};
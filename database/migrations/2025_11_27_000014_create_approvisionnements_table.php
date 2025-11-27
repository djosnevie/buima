<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('approvisionnements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etablissement_id')->constrained()->onDelete('cascade');
            $table->foreignId('fournisseur_id')->nullable()->constrained()->onDelete('set null');
            $table->string('numero_bon_livraison')->nullable();
            $table->date('date_approvisionnement');
            $table->date('date_reception')->nullable();
            $table->enum('statut', ['en_attente', 'recu', 'partiel', 'annule'])->default('en_attente');
            $table->decimal('montant_total', 10, 2)->default(0);
            $table->foreignId('user_id')->constrained(); // qui a créé
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['etablissement_id', 'statut', 'date_approvisionnement'], 'approv_etab_statut_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approvisionnements');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etablissement_id')->constrained()->onDelete('cascade');
            $table->foreignId('table_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('caisse_id')->nullable(); // Constrained in caisses migration
            $table->string('numero_commande', 50)->unique();
            $table->enum('type_commande', ['sur_place', 'emporter', 'livraison'])->default('sur_place');

            // Informations client
            $table->string('client_nom')->nullable();
            $table->string('client_telephone', 20)->nullable();
            $table->text('client_adresse')->nullable();

            // Gestion
            $table->foreignId('user_id')->constrained(); // Serveur/Caissier
            $table->enum('statut', [
                'en_attente',
                'en_preparation',
                'prete',
                'servie',
                'livree',
                'annulee',
                'payee'
            ])->default('en_attente');

            // Montants
            $table->decimal('sous_total', 10, 2)->default(0);
            $table->decimal('montant_reduction', 10, 2)->default(0);
            $table->decimal('montant_taxes', 10, 2)->default(0);
            $table->decimal('montant_livraison', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            // Dates
            $table->dateTime('date_commande');
            $table->dateTime('heure_prise');
            $table->dateTime('heure_preparation')->nullable();
            $table->dateTime('heure_service')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['etablissement_id', 'statut', 'date_commande']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
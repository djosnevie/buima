<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained()->onDelete('cascade');
            $table->string('numero_facture', 50)->unique();
            $table->decimal('montant_total', 10, 2);
            $table->decimal('montant_ht', 10, 2);
            $table->decimal('montant_tva', 10, 2);
            $table->decimal('montant_paye', 10, 2)->default(0);
            $table->decimal('montant_rendu', 10, 2)->default(0);
            $table->enum('mode_paiement', [
                'especes', 
                'carte_bancaire', 
                'cheque', 
                'virement', 
                'ticket_restaurant',
                'mobile_money'
            ])->nullable();
            $table->enum('statut', ['en_attente', 'payee', 'annulee'])->default('en_attente');
            $table->dateTime('date_emission');
            $table->dateTime('date_paiement')->nullable();
            $table->string('fichier_pdf')->nullable();
            $table->timestamps();
            
            $table->index(['numero_facture', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
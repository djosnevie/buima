<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etablissement_id')->constrained()->onDelete('cascade');
            $table->foreignId('session_caisse_id')->nullable()->constrained('sessions_caisse')->onDelete('set null');
            $table->foreignId('facture_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['vente', 'depense', 'approvisionnement', 'ajustement']);
            $table->decimal('montant', 10, 2);
            $table->enum('mode_paiement', [
                'especes',
                'carte_bancaire',
                'cheque',
                'virement',
                'ticket_restaurant',
                'mobile_money'
            ]);
            $table->text('description')->nullable();
            $table->string('statut')->default('complete'); // pending, complete, failed
            $table->foreignId('user_id')->constrained();
            $table->dateTime('date_transaction');
            $table->timestamps();

            $table->index(['etablissement_id', 'type', 'date_transaction']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
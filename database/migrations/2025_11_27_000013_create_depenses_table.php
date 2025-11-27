<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etablissement_id')->constrained()->onDelete('cascade');
            $table->foreignId('transaction_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('categorie', [
                'achat_stock',
                'salaires',
                'loyer',
                'electricite',
                'eau',
                'maintenance',
                'marketing',
                'autre'
            ]);
            $table->decimal('montant', 10, 2);
            $table->string('fournisseur')->nullable();
            $table->text('description');
            $table->string('numero_facture')->nullable();
            $table->date('date_depense');
            $table->string('justificatif')->nullable(); // fichier
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
            
            $table->index(['etablissement_id', 'categorie', 'date_depense']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depenses');
    }
};
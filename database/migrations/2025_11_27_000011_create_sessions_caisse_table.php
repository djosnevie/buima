<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sessions_caisse', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caisse_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained(); // Caissier
            $table->dateTime('date_ouverture');
            $table->dateTime('date_fermeture')->nullable();
            $table->decimal('montant_ouverture', 10, 2)->default(0);
            $table->decimal('montant_fermeture_theorique', 10, 2)->default(0);
            $table->decimal('montant_fermeture_reel', 10, 2)->nullable();
            $table->decimal('ecart', 10, 2)->default(0);
            $table->enum('statut', ['ouverte', 'fermee'])->default('ouverte');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['caisse_id', 'statut', 'date_ouverture']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions_caisse');
    }
};
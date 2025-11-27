<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etablissement_id')->constrained()->onDelete('cascade');
            $table->string('reference')->unique();
            $table->date('date_inventaire');
            $table->enum('type', ['complet', 'partiel']);
            $table->enum('statut', ['en_cours', 'termine', 'valide'])->default('en_cours');
            $table->foreignId('user_id')->constrained(); // qui fait l'inventaire
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['etablissement_id', 'date_inventaire']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventaires');
    }
};
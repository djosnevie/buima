<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etablissement_id')->constrained()->onDelete('cascade');
            $table->string('numero', 50);
            $table->integer('capacite')->default(4);
            $table->string('zone', 100)->nullable(); // terrasse, salle, étage
            $table->enum('statut', ['libre', 'occupee', 'reservee', 'hors_service'])->default('libre');
            $table->string('qr_code')->nullable();
            $table->timestamps();
            
            $table->unique(['etablissement_id', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
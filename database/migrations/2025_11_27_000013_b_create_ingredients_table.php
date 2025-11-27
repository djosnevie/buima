<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etablissement_id')->constrained()->onDelete('cascade');
            $table->string('nom');
            $table->string('code')->nullable();
            $table->string('unite', 20)->default('kg'); // kg, l, piece, etc.
            $table->decimal('prix_achat_moyen', 10, 2)->default(0);
            $table->decimal('stock_actuel', 10, 3)->default(0);
            $table->decimal('seuil_alerte', 10, 3)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};

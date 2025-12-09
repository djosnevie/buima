<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            // Drop foreign key first if needed, but often modifying the column works if constraint allows nulls?
            // Actually, usually you need to drop constraint, modify column, add constraint.
            // But let's try the simple way first which works on many drivers now.
            $table->foreignId('categorie_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->foreignId('categorie_id')->nullable(false)->change();
        });
    }
};

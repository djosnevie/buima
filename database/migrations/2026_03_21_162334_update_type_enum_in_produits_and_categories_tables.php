<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify ENUM column in produits
        DB::statement("ALTER TABLE produits MODIFY type ENUM('entree', 'plat', 'dessert', 'boisson', 'accompagnement', 'autre') DEFAULT 'autre'");
        
        // Modify ENUM column in categories
        DB::statement("ALTER TABLE categories MODIFY type ENUM('entree', 'plat', 'dessert', 'boisson', 'accompagnement', 'autre') DEFAULT 'autre'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Careful with rollback on ENUM, better to leave it expanded or handle defaults.
    }
};

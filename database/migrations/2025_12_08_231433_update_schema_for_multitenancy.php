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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('etablissement_id')->nullable()->after('id')->constrained('etablissements')->onDelete('set null');
        });

        Schema::table('etablissements', function (Blueprint $table) {
            $table->string('devise', 5)->default('XAF')->after('type'); // Code ISO 4217, e.g., EUR, USD, XAF
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['etablissement_id']);
            $table->dropColumn('etablissement_id');
        });

        Schema::table('etablissements', function (Blueprint $table) {
            $table->dropColumn('devise');
        });
    }
};

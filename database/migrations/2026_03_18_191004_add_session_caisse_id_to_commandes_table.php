<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->foreignId('session_caisse_id')
                ->nullable()
                ->after('caisse_id')
                ->constrained('sessions_caisse')
                ->onDelete('set null');

            $table->index('session_caisse_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropForeign(['session_caisse_id']);
            $table->dropColumn('session_caisse_id');
        });
    }
};

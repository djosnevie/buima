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
        Schema::table('sessions_caisse', function (Blueprint $table) {
            $table->renameColumn('fond_caisse_depart', 'montant_ouverture');
            $table->renameColumn('fond_caisse_theorique', 'montant_fermeture_theorique');
            $table->renameColumn('fond_caisse_reel', 'montant_fermeture_reel');
            $table->renameColumn('commentaire', 'notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sessions_caisse', function (Blueprint $table) {
            $table->renameColumn('montant_ouverture', 'fond_caisse_depart');
            $table->renameColumn('montant_fermeture_theorique', 'fond_caisse_theorique');
            $table->renameColumn('montant_fermeture_reel', 'fond_caisse_reel');
            $table->renameColumn('notes', 'commentaire');
        });
    }
};

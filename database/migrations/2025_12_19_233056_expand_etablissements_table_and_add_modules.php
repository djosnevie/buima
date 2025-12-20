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
        Schema::table('etablissements', function (Blueprint $table) {
            $table->string('rccm')->nullable();
            $table->string('nui')->nullable();
            $table->string('site_web')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->text('description')->nullable();
            $table->boolean('tva_applicable')->default(false);
            $table->decimal('tva_taux', 5, 2)->default(0);
            $table->json('modules')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etablissements', function (Blueprint $table) {
            $table->dropColumn([
                'rccm',
                'nui',
                'site_web',
                'facebook',
                'instagram',
                'description',
                'tva_applicable',
                'tva_taux',
                'modules'
            ]);
        });
    }
};

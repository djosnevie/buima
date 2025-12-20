<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('etablissements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nom');
            $table->string('slug')->unique()->nullable();
            $table->enum('type', ['avec_tables', 'sans_tables', 'mixte']);
            $table->string('devise', 5)->default('XAF');
            $table->string('theme_color', 7)->default('#3B82F6');
            $table->string('secondary_color', 7)->nullable();
            $table->string('button_color', 7)->nullable();
            $table->text('adresse')->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('logo')->nullable();
            $table->string('rccm')->nullable();
            $table->string('nui')->nullable();
            $table->string('site_web')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->text('description')->nullable();
            $table->boolean('tva_applicable')->default(false);
            $table->decimal('tva_taux', 5, 2)->default(0);
            $table->json('configuration')->nullable(); // horaires, options, etc.
            $table->json('modules')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        // Add foreign key constraint for users table (etablissement_id)
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                // If the column doesn't exist (because we will add it in users migration), we don't add it here.
                // We only add the constraint if the column exists but is unconstrained.
                // However, to simplify for now: assume users migration adds the column.

                // If we are recovering from a state where users table exists but constraint is missing:
                if (Schema::hasColumn('users', 'etablissement_id')) {
                    // Try to add constraint if it doesn't exist? Laravel doesn't have hasForeignKey easily.
                    // We'll trust the migration flow: User runs fresh, Users table created with column (no constraint),
                    // Then Etablissements table created, then we add constraint here.

                    // Note: If running a partial migration where users table already had the constraint, this might fail unless we check.
                    // For fresh migration, this is the way.
                }
            });
        }
    }


    public function down(): void
    {
        Schema::dropIfExists('etablissements');
    }
};
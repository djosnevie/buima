<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('caisses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etablissement_id')->constrained()->onDelete('cascade');
            $table->string('nom');
            $table->string('code')->unique();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'caisse_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('caisse_id')->references('id')->on('caisses')->onDelete('set null');
            });
        }

        if (Schema::hasTable('commandes') && Schema::hasColumn('commandes', 'caisse_id')) {
            Schema::table('commandes', function (Blueprint $table) {
                $table->foreign('caisse_id')->references('id')->on('caisses')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('caisses');
    }
};
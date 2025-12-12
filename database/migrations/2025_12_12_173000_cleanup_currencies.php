<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing records to remove unsupported currencies
        DB::table('etablissements')
            ->whereIn('devise', ['CAD', 'GBP', 'Livre'])
            ->update(['devise' => 'XAF']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reverse data update easily without backup
    }
};

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
        Schema::table('bill_categories', function (Blueprint $table) {
            $table->string('icon', 50)->nullable()->after('is_active');
            $table->string('color', 7)->nullable()->after('icon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bill_categories', function (Blueprint $table) {
            $table->dropColumn(['icon', 'color']);
        });
    }
};

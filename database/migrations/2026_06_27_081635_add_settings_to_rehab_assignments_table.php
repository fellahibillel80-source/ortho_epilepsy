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
        Schema::table('rehab_assignments', function (Blueprint $table) {
            $table->string('difficulty')->default('beginner'); // beginner, medium, advanced
            $table->integer('duration_minutes')->default(5);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rehab_assignments', function (Blueprint $table) {
            $table->dropColumn(['difficulty', 'duration_minutes']);
        });
    }
};

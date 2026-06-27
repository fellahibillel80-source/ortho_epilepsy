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
        Schema::create('rehab_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('rehab_assignments')->onDelete('cascade');
            $table->float('accuracy_percentage')->nullable();
            $table->integer('avg_reaction_time_ms')->nullable();
            $table->integer('total_errors')->default(0);
            $table->integer('missed_responses')->default(0);
            $table->json('trials')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rehab_results');
    }
};

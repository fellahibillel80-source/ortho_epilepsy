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
        Schema::create('seizure_trigger', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seizure_id');
            $table->unsignedBigInteger('trigger_id');
            $table->timestamps();

            $table->foreign('seizure_id')->references('id')->on('seizures')->onDelete('cascade');
            $table->foreign('trigger_id')->references('id')->on('triggers')->onDelete('cascade');
            $table->unique(['seizure_id', 'trigger_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seizure_trigger');
    }
};

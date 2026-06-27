<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignment_id')->nullable();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('test_id');
            $table->integer('score'); // Correct trials or points
            $table->double('duration_seconds'); // Time taken to complete the test
            $table->integer('errors_count'); // Wrong trials
            $table->json('raw_details')->nullable(); // JSON payload for details per trial (e.g. response times)
            $table->timestamps();

            $table->foreign('assignment_id')->references('id')->on('test_assignments')->onDelete('set null');
            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('test_id')->references('id')->on('cognitive_tests')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_results');
    }
};

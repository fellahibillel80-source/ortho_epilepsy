<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('specialist_id');
            $table->unsignedBigInteger('test_id');
            $table->tinyInteger('status')->default(0); // 0 = pending, 1 = completed
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('specialist_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('test_id')->references('id')->on('cognitive_tests')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_assignments');
    }
};

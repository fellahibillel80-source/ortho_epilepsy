<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_specialist', function (Blueprint $table) {
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('specialist_id');
            $table->tinyInteger('status')->default(0); // 0 = pending, 1 = linked/approved
            $table->timestamps();

            $table->primary(['patient_id', 'specialist_id']);
            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('specialist_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_specialist');
    }
};

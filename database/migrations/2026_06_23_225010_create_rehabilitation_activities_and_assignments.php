<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rehabilitation_activities', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // e.g. 'speech-rehab-1', 'memory-focus-2'
            $table->string('name_ar');
            $table->string('name_en');
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->string('category'); // 'speech', 'cognitive', 'motor'
            $table->timestamps();
        });

        Schema::create('rehab_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('specialist_id');
            $table->unsignedBigInteger('activity_id');
            $table->string('status')->default('assigned'); // 'assigned', 'in_progress', 'completed'
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('specialist_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('activity_id')->references('id')->on('rehabilitation_activities')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rehab_assignments');
        Schema::dropIfExists('rehabilitation_activities');
    }
};

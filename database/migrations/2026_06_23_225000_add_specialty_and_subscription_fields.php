<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('specialty')->nullable()->after('role'); // e.g. 'speech_therapist', 'neurologist'
        });

        Schema::table('clinics', function (Blueprint $table) {
            $table->string('subscription_status')->default('active')->after('status'); // 'active', 'suspended', 'expired'
            $table->string('subscription_plan')->default('standard')->after('subscription_status'); // 'standard', 'premium'
            $table->timestamp('subscription_ends_at')->nullable()->after('subscription_plan');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('specialty');
        });

        Schema::table('clinics', function (Blueprint $table) {
            $table->dropColumn(['subscription_status', 'subscription_plan', 'subscription_ends_at']);
        });
    }
};

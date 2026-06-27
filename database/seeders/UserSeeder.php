<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create a Test Clinic
        $clinicId = DB::table('clinics')->insertGetId([
            'name' => 'عيادة الأمل التخصصية للصرع',
            'address' => 'شارع الصحة، الجزائر',
            'phone' => '0555123456',
            'status' => 1,
            'subscription_status' => 'active',
            'subscription_plan' => 'premium',
            'subscription_ends_at' => now()->addMonths(6),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Create Super Admin
        User::create([
            'name' => 'مدير النظام العام',
            'email' => 'super@test.com',
            'password' => Hash::make('12345678'),
            'role' => 'super_admin',
        ]);

        // 3. Create Clinic Admin
        User::create([
            'name' => 'مدير العيادة',
            'email' => 'clinic@test.com',
            'password' => Hash::make('12345678'),
            'role' => 'clinic_admin',
            'clinic_id' => $clinicId,
        ]);

        // 4. Create Specialist
        $specialist = User::create([
            'name' => 'الدكتور أحمد سليمان',
            'email' => 'specialist@test.com',
            'password' => Hash::make('12345678'),
            'role' => 'specialist',
            'specialty' => 'طبيب أعصاب وصوتيات',
            'clinic_id' => $clinicId,
        ]);
        $specialistId = $specialist->id;

        // 5. Create/Update Caregiver
        $caregiver = User::create([
            'name' => 'المرافق الشخصي',
            'email' => 'caregiver@test.com',
            'password' => Hash::make('12345678'),
            'role' => 'caregiver',
        ]);
        $caregiverId = $caregiver->id;

        // 6. Create/Update Patient
        $patient = User::create([
            'name' => 'المريض تجربة',
            'email' => 'test@test.com',
            'password' => Hash::make('12345678'),
            'role' => 'patient',
            'emergency_contact' => '0555987654',
            'caregiver_id' => $caregiverId,
            'clinic_id' => $clinicId,
        ]);
        $patientId = $patient->id;

        // 7. Link Patient and Specialist
        DB::table('patient_specialist')->insertOrIgnore([
            'patient_id' => $patientId,
            'specialist_id' => $specialistId,
            'status' => 1, // approved
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 8. Assign all 6 tests to the patient by default for instant testing
        for ($testId = 1; $testId <= 6; $testId++) {
            DB::table('test_assignments')->insertOrIgnore([
                'patient_id' => $patientId,
                'specialist_id' => $specialistId,
                'test_id' => $testId,
                'status' => 0, // pending
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

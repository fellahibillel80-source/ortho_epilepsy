<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$test = [
    'slug' => 'cognitive-flexibility',
    'name_ar' => 'وحدة المرونة الذهنية (المعالج الذكي)',
    'name_en' => 'Cognitive Flexibility (AI)',
    'description_ar' => 'جلسة تفاعلية مع المعالج الذكي لتدريب المرونة الذهنية والتكيف.',
    'description_en' => 'Interactive session with AI therapist for cognitive flexibility.',
    'executive_function' => 'Cognitive Flexibility',
];

$testModel = \App\Models\CognitiveTest::firstOrCreate(['slug' => $test['slug']], $test);

$patient = \App\Models\User::where('role', 'patient')->first();
$specialist = \App\Models\User::where('role', 'specialist')->first();

if ($patient && $specialist) {
    \App\Models\TestAssignment::firstOrCreate([
        'patient_id' => $patient->id,
        'specialist_id' => $specialist->id,
        'test_id' => $testModel->id,
        'status' => 'pending'
    ]);
    echo "Cognitive Flexibility Test seeded and assigned to patient successfully!\n";
} else {
    echo "Test seeded, but patient or specialist not found for assignment.\n";
}

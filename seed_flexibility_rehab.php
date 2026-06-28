<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$activityData = [
    'slug' => 'flexibility-task',
    'name_ar' => 'وحدة المرونة الذهنية (المعالج الذكي)',
    'name_en' => 'Cognitive Flexibility (AI)',
    'description_ar' => 'جلسة تفاعلية مع المعالج الذكي لتدريب المرونة الذهنية والتكيف للمرضى.',
    'description_en' => 'Interactive session with AI therapist for cognitive flexibility.',
    'category' => 'cognitive',
];

$activity = \App\Models\RehabilitationActivity::firstOrCreate(['slug' => $activityData['slug']], $activityData);

$patient = \App\Models\User::where('role', 'patient')->first();
$specialist = \App\Models\User::where('role', 'specialist')->first();

if ($patient && $specialist) {
    \App\Models\RehabAssignment::create([
        'patient_id' => $patient->id,
        'specialist_id' => $specialist->id,
        'activity_id' => $activity->id,
        'status' => 'pending',
        'difficulty' => 'beginner',
        'duration_minutes' => 15
    ]);
    echo "Flexibility activity seeded and assigned to patient successfully!\n";
} else {
    echo "Activity seeded, but patient or specialist not found for assignment.\n";
}

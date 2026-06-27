<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$activities = [
    ['slug' => 'sorting-task', 'name_ar' => 'لعبة فرز الكرات (Drag & Drop)', 'name_en' => 'Ball Sorting Task', 'description_ar' => 'تدريب التآزر البصري الحركي والانتباه', 'description_en' => 'Visual motor sorting task', 'category' => 'cognitive']
];

$patient = \App\Models\User::where('role', 'patient')->first();
$specialist = \App\Models\User::where('role', 'specialist')->first();

if (!$patient || !$specialist) {
    die("Patient or Specialist not found\n");
}

foreach($activities as $act) {
    $model = \App\Models\RehabilitationActivity::firstOrCreate(['slug' => $act['slug']], $act);
    \App\Models\RehabAssignment::create([
        'patient_id' => $patient->id,
        'specialist_id' => $specialist->id,
        'activity_id' => $model->id,
        'status' => 'pending',
        'difficulty' => 'beginner',
        'duration_minutes' => 5
    ]);
}

echo "Sorting activity seeded and assigned successfully!\n";

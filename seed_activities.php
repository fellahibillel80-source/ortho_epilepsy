<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$activities = [
    ['slug' => 'stroop-task', 'name_ar' => 'مهمة الانتباه الانتقائي (Stroop)', 'name_en' => 'Stroop Task', 'description_ar' => 'نشاط تفاعلي لقياس الانتباه وسرعة الاستجابة', 'description_en' => 'Interactive attention task', 'category' => 'cognitive'],
    ['slug' => 'gonogo-task', 'name_ar' => 'الكف الحركي (اذهب / لا تذهب)', 'name_en' => 'Go/No-Go Task', 'description_ar' => 'تدريب التوقف عن الاستجابة الخاطئة بسرعة', 'description_en' => 'Motor inhibition task', 'category' => 'cognitive'],
    ['slug' => 'memory-task', 'name_ar' => 'الذاكرة المكانية', 'name_en' => 'Spatial Memory Task', 'description_ar' => 'تدريب على حفظ أنماط بصرية قصيرة المدى', 'description_en' => 'Short-term spatial memory task', 'category' => 'cognitive']
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

echo "Activities seeded and assigned to patient successfully!\n";

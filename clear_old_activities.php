<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\RehabAssignment::whereNotIn('activity_id', function($query) {
    $query->select('id')->from('rehabilitation_activities')
          ->whereIn('slug', ['stroop-task', 'gonogo-task', 'memory-task']);
})->delete();

echo "Old assignments deleted successfully!\n";

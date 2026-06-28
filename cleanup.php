<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$clinic = App\Models\Clinic::where('name', 'like', '%عيادة الأمل%')->first();

if (!$clinic) {
    echo "Clinic not found.\n";
    exit;
}

echo "Found clinic: " . $clinic->name . " with ID: " . $clinic->id . "\n";

$deletedSpecialists = App\Models\User::where('role', 'specialist')
    ->where(function($query) use ($clinic) {
        $query->whereNull('clinic_id')
              ->orWhere('clinic_id', '!=', $clinic->id);
    })->delete();

$deletedPatients = App\Models\User::where('role', 'patient')
    ->where(function($query) use ($clinic) {
        $query->whereNull('clinic_id')
              ->orWhere('clinic_id', '!=', $clinic->id);
    })->delete();

echo "Deleted {$deletedSpecialists} unlinked specialists.\n";
echo "Deleted {$deletedPatients} unlinked patients.\n";

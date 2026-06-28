<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo json_encode(App\Models\User::get(["id", "email", "role", "clinic_id"]), JSON_PRETTY_PRINT);

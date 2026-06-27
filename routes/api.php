<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

use App\Http\Controllers\SeizureController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\TriggerController;
use App\Http\Controllers\MedicationScheduleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmergencyController;
use App\Http\Controllers\ClinicController;
use App\Http\Controllers\PatientSpecialistController;
use App\Http\Controllers\CognitiveTestController;
use App\Http\Controllers\RehabilitationController;
use App\Http\Controllers\SuggestionController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/debug-seed', function () {
    try {
        // Run seed logic directly to bypass autoloader issues
        \App\Models\User::firstOrCreate(
            ['email' => 'test@test.com'],
            ['name' => 'المريض تجربة', 'password' => \Illuminate\Support\Facades\Hash::make('12345678'), 'role' => 'patient']
        );
        \App\Models\User::firstOrCreate(
            ['email' => 'specialist@test.com'],
            ['name' => 'الدكتور', 'password' => \Illuminate\Support\Facades\Hash::make('12345678'), 'role' => 'specialist']
        );
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@test.com'],
            ['name' => 'مدير العيادة', 'password' => \Illuminate\Support\Facades\Hash::make('12345678'), 'role' => 'clinic_admin']
        );
        \App\Models\User::firstOrCreate(
            ['email' => 'super@test.com'],
            ['name' => 'المدير العام', 'password' => \Illuminate\Support\Facades\Hash::make('12345678'), 'role' => 'super_admin']
        );
        
        return response()->json([
            'status' => 'success',
            'users' => \App\Models\User::all()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('seizures', SeizureController::class);
    Route::apiResource('medications', MedicationController::class);
    Route::apiResource('triggers', TriggerController::class);
    Route::apiResource('medication-schedules', MedicationScheduleController::class);

    // Emergency Features
    Route::post('/emergency/trigger', [EmergencyController::class, 'trigger']);
    Route::post('/emergency/stop', [EmergencyController::class, 'stop']);
    Route::get('/emergency/status', [EmergencyController::class, 'status']);

    // Clinic Management
    Route::get('/admin/clinics', [ClinicController::class, 'index']);
    Route::post('/admin/clinics', [ClinicController::class, 'store']);
    Route::post('/clinic/specialists', [ClinicController::class, 'registerSpecialist']);
    Route::get('/clinic/specialists', [ClinicController::class, 'getSpecialists']);
    Route::post('/clinic/patients', [ClinicController::class, 'registerPatient']);

    // Patient-Specialist Linking
    Route::post('/specialist/patients/invite', [PatientSpecialistController::class, 'linkPatient']);
    Route::get('/patient/pending-specialists', [PatientSpecialistController::class, 'getPendingRequests']);
    Route::post('/patient/accept-specialist', [PatientSpecialistController::class, 'acceptRequest']);
    Route::get('/specialist/my-patients', [PatientSpecialistController::class, 'getMyPatients']);

    // Cognitive Tests & Super Admin Addition
    Route::get('/cognitive-tests', [CognitiveTestController::class, 'listTests']);
    Route::post('/admin/cognitive-tests', [CognitiveTestController::class, 'storeTest']);
    Route::post('/specialist/tests/assign', [CognitiveTestController::class, 'assignTest']);
    Route::get('/patient/tests/assigned', [CognitiveTestController::class, 'getPendingAssignments']);
    Route::post('/patient/tests/results', [CognitiveTestController::class, 'submitResult']);
    Route::get('/specialist/patients/{id}/test-results', [CognitiveTestController::class, 'getPatientResults']);

    // Direct Patient-Specialist Linking (Clinic Admin) and Patients List
    Route::get('/clinic/patients', [ClinicController::class, 'getPatients']);
    Route::post('/clinic/link-patient-specialist', [ClinicController::class, 'linkPatientSpecialist']);
    Route::put('/admin/clinics/{id}/subscription', [ClinicController::class, 'updateSubscription']);

    // Rehabilitation Activities
    Route::get('/rehab-activities', [RehabilitationController::class, 'listActivities']);
    Route::post('/admin/rehab-activities', [RehabilitationController::class, 'storeActivity']);
    Route::post('/specialist/rehab/assign', [RehabilitationController::class, 'assignActivity']);
    Route::get('/specialist/rehab/assigned/{patient_id}', [RehabilitationController::class, 'getPatientAssignments']);
    Route::get('/patient/rehab/assigned', [RehabilitationController::class, 'getPendingAssignments']);
    Route::put('/rehab/assigned/{id}/complete', [RehabilitationController::class, 'completeAssignment']);

    // Specialist Suggestions
    Route::post('/specialist/suggestions', [SuggestionController::class, 'propose']);
    Route::get('/suggestions', [SuggestionController::class, 'listSuggestions']);
    Route::put('/admin/suggestions/{id}/approve', [SuggestionController::class, 'approve']);
    Route::put('/admin/suggestions/{id}/reject', [SuggestionController::class, 'reject']);
});

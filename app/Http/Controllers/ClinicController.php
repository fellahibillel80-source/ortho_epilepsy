<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ClinicController extends Controller
{
    // Super Admin: List all clinics
    public function index(Request $request)
    {
        if ($request->user()->role !== 'super_admin') {
            return response()->json(['message' => 'غير مصرح لك بالوصول.'], 403);
        }

        $clinics = Clinic::withCount('users')->get();
        return response()->json($clinics);
    }

    // Super Admin: Create a new clinic and its admin user
    public function store(Request $request)
    {
        if ($request->user()->role !== 'super_admin') {
            return response()->json(['message' => 'غير مصرح لك بالوصول.'], 403);
        }

        $validated = $request->validate([
            'clinic_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|string|email|max:255|unique:users,email',
            'admin_password' => 'required|string|min:8',
        ]);

        $clinic = Clinic::create([
            'name' => $validated['clinic_name'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
        ]);

        $admin = User::create([
            'name' => $validated['admin_name'],
            'email' => $validated['admin_email'],
            'password' => Hash::make($validated['admin_password']),
            'role' => 'clinic_admin',
            'clinic_id' => $clinic->id,
        ]);

        return response()->json([
            'message' => 'تم إنشاء العيادة وحساب مدير العيادة بنجاح.',
            'clinic' => $clinic,
            'admin' => $admin,
        ], 201);
    }

    // Clinic Admin: Register a specialist/doctor for this clinic
    public function registerSpecialist(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'clinic_admin' || !$user->clinic_id) {
            return response()->json(['message' => 'غير مصرح لك بالوصول كمدير عيادة.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'specialty' => 'nullable|string|max:255',
        ]);

        $specialist = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'specialist',
            'specialty' => $validated['specialty'] ?? 'أخصائي صرع',
            'clinic_id' => $user->clinic_id,
        ]);

        return response()->json([
            'message' => 'تم تسجيل حساب الطبيب/المختص بنجاح.',
            'specialist' => $specialist,
        ], 201);
    }

    // Clinic Admin: List specialists in this clinic
    public function getSpecialists(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'clinic_admin' || !$user->clinic_id) {
            return response()->json(['message' => 'غير مصرح لك.'], 403);
        }

        $specialists = User::where('clinic_id', $user->clinic_id)
                           ->where('role', 'specialist')
                           ->get();
        return response()->json($specialists);
    }

    // Super Admin: Update subscription details
    public function updateSubscription(Request $request, $id)
    {
        if ($request->user()->role !== 'super_admin') {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $clinic = Clinic::findOrFail($id);
        $validated = $request->validate([
            'subscription_status' => 'required|string|in:active,suspended,expired',
            'subscription_plan' => 'required|string|in:standard,premium',
            'subscription_ends_at' => 'nullable|date',
        ]);

        $clinic->update($validated);

        return response()->json([
            'message' => 'تم تحديث تفاصيل الاشتراك المالي للعيادة بنجاح.',
            'clinic' => $clinic
        ]);
    }

    // Clinic Admin: Get all patients registered in this clinic
    public function getPatients(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'clinic_admin' || !$user->clinic_id) {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $patients = User::where('clinic_id', $user->clinic_id)
                        ->where('role', 'patient')
                        ->with('specialists')
                        ->get();

        return response()->json($patients);
    }

    // Clinic Admin: Link a patient directly to a specialist in this clinic
    public function linkPatientSpecialist(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'clinic_admin' || !$user->clinic_id) {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $validated = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'specialist_id' => 'required|exists:users,id',
        ]);

        $patient = User::where('id', $validated['patient_id'])
                       ->where('clinic_id', $user->clinic_id)
                       ->where('role', 'patient')
                       ->firstOrFail();

        $specialist = User::where('id', $validated['specialist_id'])
                          ->where('clinic_id', $user->clinic_id)
                          ->where('role', 'specialist')
                          ->firstOrFail();

        // Link them with approved status (1)
        $patient->specialists()->syncWithoutDetaching([$specialist->id => ['status' => 1]]);

        return response()->json(['message' => 'تم ربط المريض بالمختص بنجاح.']);
    }

    // Clinic Admin: Register a new patient for this clinic
    public function registerPatient(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'clinic_admin' || !$user->clinic_id) {
            return response()->json(['message' => 'غير مصرح لك بالوصول كمدير عيادة.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'emergency_contact' => 'nullable|string|max:255',
        ]);

        $patient = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'patient',
            'emergency_contact' => $validated['emergency_contact'] ?? null,
            'clinic_id' => $user->clinic_id,
        ]);

        return response()->json([
            'message' => 'تم تسجيل المريض بنجاح وربطه بالعيادة.',
            'patient' => $patient,
        ], 201);
    }
}

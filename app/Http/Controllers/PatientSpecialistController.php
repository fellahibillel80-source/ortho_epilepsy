<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class PatientSpecialistController extends Controller
{
    // Specialist: Initiate linking with a patient via email
    public function linkPatient(Request $request)
    {
        $specialist = $request->user();
        if ($specialist->role !== 'specialist') {
            return response()->json(['message' => 'غير مصرح لك بالوصول كمختص.'], 403);
        }

        $validated = $request->validate([
            'patient_email' => 'required|string|email|exists:users,email',
        ]);

        $patient = User::where('email', $validated['patient_email'])
                       ->where('role', 'patient')
                       ->firstOrFail();

        // Check if already linked or requested
        $existing = $specialist->patients()->where('patient_id', $patient->id)->first();
        if ($existing) {
            if ($existing->pivot->status === 1) {
                return response()->json(['message' => 'المريض مرتبط بك بالفعل.'], 400);
            }
            return response()->json(['message' => 'لقد تم إرسال طلب ربط مسبقاً وهو بانتظار الموافقة.'], 400);
        }

        // Attach with pending status (0)
        $specialist->patients()->attach($patient->id, ['status' => 0]);

        return response()->json([
            'message' => 'تم إرسال طلب الارتباط إلى المريض بنجاح.',
            'patient' => [
                'id' => $patient->id,
                'name' => $patient->name,
                'email' => $patient->email,
            ]
        ]);
    }

    // Patient: View pending linking requests
    public function getPendingRequests(Request $request)
    {
        $patient = $request->user();
        if ($patient->role !== 'patient') {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $requests = $patient->specialists()->wherePivot('status', 0)->get();
        return response()->json($requests);
    }

    // Patient: Accept linking request from a specialist
    public function acceptRequest(Request $request)
    {
        $patient = $request->user();
        if ($patient->role !== 'patient') {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $validated = $request->validate([
            'specialist_id' => 'required|exists:users,id',
        ]);

        $specialist = $patient->specialists()->where('specialist_id', $validated['specialist_id'])->firstOrFail();

        // Update status to approved (1)
        $patient->specialists()->updateExistingPivot($validated['specialist_id'], ['status' => 1]);

        return response()->json(['message' => 'تمت الموافقة على الارتباط بالطبيب بنجاح.']);
    }

    // Specialist: Get list of active/approved patients
    public function getMyPatients(Request $request)
    {
        $specialist = $request->user();
        if ($specialist->role !== 'specialist') {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $patients = $specialist->patients()->wherePivot('status', 1)->get();
        return response()->json($patients);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RehabilitationActivity;
use App\Models\RehabAssignment;
use App\Models\User;

class RehabilitationController extends Controller
{
    // List all rehabilitation activities
    public function listActivities()
    {
        $activities = RehabilitationActivity::all();
        return response()->json($activities);
    }

    // Super Admin: Store new rehab activity
    public function storeActivity(Request $request)
    {
        if ($request->user()->role !== 'super_admin') {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'category' => 'required|string|max:255',
        ]);

        // Generate slug from English name
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $validated['name_en'])));

        $activity = RehabilitationActivity::create([
            'slug' => $slug,
            'name_ar' => $validated['name_ar'],
            'name_en' => $validated['name_en'],
            'description_ar' => $validated['description_ar'],
            'description_en' => $validated['description_en'],
            'category' => $validated['category'],
        ]);

        return response()->json([
            'message' => 'تم إضافة النشاط التأهيلي بنجاح.',
            'activity' => $activity
        ], 201);
    }

    // Specialist: Assign a rehab activity to a patient
    public function assignActivity(Request $request)
    {
        $specialist = $request->user();
        if ($specialist->role !== 'specialist') {
            return response()->json(['message' => 'غير مصرح كمختص.'], 403);
        }

        $validated = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'activity_id' => 'required|exists:rehabilitation_activities,id',
            'difficulty' => 'nullable|string|in:beginner,medium,advanced',
            'duration_minutes' => 'nullable|integer|min:1|max:60',
            'notes' => 'nullable|string',
        ]);

        // Verify patient linkage
        $isLinked = $specialist->patients()
                              ->where('patient_id', $validated['patient_id'])
                              ->wherePivot('status', 1)
                              ->exists();

        if (!$isLinked) {
            return response()->json(['message' => 'المريض غير مرتبط بك.'], 400);
        }

        $assignment = RehabAssignment::create([
            'patient_id' => $validated['patient_id'],
            'specialist_id' => $specialist->id,
            'activity_id' => $validated['activity_id'],
            'status' => 'assigned',
            'difficulty' => $validated['difficulty'] ?? 'beginner',
            'duration_minutes' => $validated['duration_minutes'] ?? 5,
            'notes' => $validated['notes'] ?? '',
        ]);

        return response()->json([
            'message' => 'تم إسناد النشاط التأهيلي للمريض بنجاح.',
            'assignment' => $assignment
        ], 201);
    }

    // Specialist: Get assignments for a specific patient
    public function getPatientAssignments(Request $request, $patientId)
    {
        $specialist = $request->user();
        if ($specialist->role !== 'specialist') {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        // Verify patient linkage
        $isLinked = $specialist->patients()
                              ->where('patient_id', $patientId)
                              ->wherePivot('status', 1)
                              ->exists();

        if (!$isLinked) {
            return response()->json(['message' => 'المريض غير مرتبك بك.'], 403);
        }

        $assignments = RehabAssignment::where('patient_id', $patientId)
                                      ->with(['activity', 'result'])
                                      ->orderBy('created_at', 'desc')
                                      ->get();

        return response()->json($assignments);
    }

    // Patient: Get their assigned rehab activities
    public function getPendingAssignments(Request $request)
    {
        $patient = $request->user();
        if ($patient->role !== 'patient') {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $assignments = RehabAssignment::where('patient_id', $patient->id)
                                      ->with(['activity', 'result'])
                                      ->orderBy('created_at', 'desc')
                                      ->get();

        return response()->json($assignments);
    }

    // Patient or Specialist: Complete a rehab activity
    public function completeAssignment(Request $request, $id)
    {
        $assignment = RehabAssignment::findOrFail($id);
        $user = $request->user();

        // Authorize: Only patient or assigning specialist
        if ($user->id !== $assignment->patient_id && $user->id !== $assignment->specialist_id) {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $validated = $request->validate([
            'accuracy_percentage' => 'nullable|numeric',
            'avg_reaction_time_ms' => 'nullable|integer',
            'total_errors' => 'nullable|integer',
            'missed_responses' => 'nullable|integer',
            'trials' => 'nullable|array',
        ]);

        $assignment->update([
            'status' => 'completed'
        ]);

        if (isset($validated['accuracy_percentage']) || isset($validated['trials'])) {
            \App\Models\RehabResult::create([
                'assignment_id' => $assignment->id,
                'accuracy_percentage' => $validated['accuracy_percentage'] ?? null,
                'avg_reaction_time_ms' => $validated['avg_reaction_time_ms'] ?? null,
                'total_errors' => $validated['total_errors'] ?? 0,
                'missed_responses' => $validated['missed_responses'] ?? 0,
                'trials' => $validated['trials'] ?? null,
            ]);
        }

        return response()->json([
            'message' => 'تم تحديث حالة النشاط إلى مكتمل.',
            'assignment' => $assignment
        ]);
    }
}

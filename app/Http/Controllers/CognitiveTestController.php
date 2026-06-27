<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CognitiveTest;
use App\Models\TestAssignment;
use App\Models\TestResult;
use App\Models\User;

class CognitiveTestController extends Controller
{
    // List all available tests (Accessible by Patient & Specialist)
    public function listTests()
    {
        $tests = CognitiveTest::all();
        return response()->json($tests);
    }

    // Specialist: Assign a test to a patient
    public function assignTest(Request $request)
    {
        $specialist = $request->user();
        if ($specialist->role !== 'specialist') {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $validated = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'test_id' => 'required|exists:cognitive_tests,id',
        ]);

        // Verify patient is linked to this specialist
        $isLinked = $specialist->patients()
                              ->where('patient_id', $validated['patient_id'])
                              ->wherePivot('status', 1)
                              ->exists();

        if (!$isLinked) {
            return response()->json(['message' => 'هذا المريض ليس مسجلاً في قائمتك.'], 400);
        }

        $assignment = TestAssignment::create([
            'patient_id' => $validated['patient_id'],
            'specialist_id' => $specialist->id,
            'test_id' => $validated['test_id'],
            'status' => 0, // pending
        ]);

        return response()->json([
            'message' => 'تم جدولة الاختبار للمريض بنجاح.',
            'assignment' => $assignment
        ], 201);
    }

    // Patient: Get pending assigned tests
    public function getPendingAssignments(Request $request)
    {
        $patient = $request->user();
        if ($patient->role !== 'patient') {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $assignments = TestAssignment::where('patient_id', $patient->id)
                                     ->where('status', 0)
                                     ->with('test')
                                     ->get();

        return response()->json($assignments);
    }

    // Patient: Submit test results after completion
    public function submitResult(Request $request)
    {
        $patient = $request->user();
        if ($patient->role !== 'patient') {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $validated = $request->validate([
            'assignment_id' => 'nullable|exists:test_assignments,id',
            'test_id' => 'required|exists:cognitive_tests,id',
            'score' => 'required|integer',
            'duration_seconds' => 'required|numeric',
            'errors_count' => 'required|integer',
            'raw_details' => 'nullable|array',
        ]);

        // If it was assigned, mark assignment as completed
        if ($validated['assignment_id']) {
            $assignment = TestAssignment::where('id', $validated['assignment_id'])
                                         ->where('patient_id', $patient->id)
                                         ->first();
            if ($assignment) {
                $assignment->update([
                    'status' => 1,
                    'completed_at' => now()
                ]);
            }
        }

        $result = TestResult::create([
            'assignment_id' => $validated['assignment_id'],
            'patient_id' => $patient->id,
            'test_id' => $validated['test_id'],
            'score' => $validated['score'],
            'duration_seconds' => $validated['duration_seconds'],
            'errors_count' => $validated['errors_count'],
            'raw_details' => $validated['raw_details'],
        ]);

        return response()->json([
            'message' => 'تم حفظ نتائج التقييم المعرفي بنجاح.',
            'result' => $result
        ], 201);
    }

    // Specialist: Get test results for a patient
    public function getPatientResults(Request $request, $patientId)
    {
        $specialist = $request->user();
        if ($specialist->role !== 'specialist') {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        // Verify patient is linked
        $isLinked = $specialist->patients()
                              ->where('patient_id', $patientId)
                              ->wherePivot('status', 1)
                              ->exists();

        if (!$isLinked) {
            return response()->json(['message' => 'المريض غير مرتبك بك.'], 403);
        }

        $results = TestResult::where('patient_id', $patientId)
                             ->with('test')
                             ->orderBy('created_at', 'desc')
                             ->get();

        return response()->json($results);
    }

    // Super Admin: Store new cognitive test
    public function storeTest(Request $request)
    {
        if ($request->user()->role !== 'super_admin') {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'executive_function' => 'required|string|max:255',
        ]);

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $validated['name_en'])));

        $test = CognitiveTest::create([
            'slug' => $slug,
            'name_ar' => $validated['name_ar'],
            'name_en' => $validated['name_en'],
            'description_ar' => $validated['description_ar'],
            'description_en' => $validated['description_en'],
            'executive_function' => $validated['executive_function'],
        ]);

        return response()->json([
            'message' => 'تم إضافة الاختبار المعرفي بنجاح.',
            'test' => $test
        ], 201);
    }
}

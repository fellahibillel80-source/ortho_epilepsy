<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use Illuminate\Http\Request;

class MedicationController extends Controller
{
    public function index()
    {
        return response()->json(Medication::with('schedules')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|integer',
            'name' => 'required|string|max:255',
            'dosage' => 'required|string|max:255',
            'schedules' => 'nullable|array',
            'schedules.*.time_of_day' => 'required|string'
        ]);

        $validated['user_id'] = $validated['user_id'] ?? 1;

        $medication = Medication::create($validated);

        if (!empty($validated['schedules'])) {
            foreach ($validated['schedules'] as $scheduleData) {
                $medication->schedules()->create($scheduleData);
            }
        }

        return response()->json($medication->load('schedules'), 201);
    }

    public function show(Medication $medication)
    {
        return response()->json($medication->load('schedules'));
    }

    public function update(Request $request, Medication $medication)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'dosage' => 'sometimes|string|max:255',
        ]);

        $medication->update($validated);

        return response()->json($medication->load('schedules'));
    }

    public function destroy(Medication $medication)
    {
        $medication->delete();
        return response()->json(null, 204);
    }
}

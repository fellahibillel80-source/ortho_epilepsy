<?php

namespace App\Http\Controllers;

use App\Models\Seizure;
use Illuminate\Http\Request;

class SeizureController extends Controller
{
    public function index()
    {
        // In a real app, filter by auth()->id()
        return response()->json(Seizure::with('triggers')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|integer',
            'start_time' => 'required|date',
            'duration_seconds' => 'required|integer',
            'type' => 'required|integer',
            'notes' => 'nullable|string',
            'triggers' => 'nullable|array'
        ]);

        // Default user_id for testing if not provided
        $validated['user_id'] = $validated['user_id'] ?? 1;

        $seizure = Seizure::create($validated);

        if (!empty($validated['triggers'])) {
            $seizure->triggers()->sync($validated['triggers']);
        }

        return response()->json($seizure->load('triggers'), 201);
    }

    public function show(Seizure $seizure)
    {
        return response()->json($seizure->load('triggers'));
    }

    public function update(Request $request, Seizure $seizure)
    {
        $validated = $request->validate([
            'start_time' => 'sometimes|date',
            'duration_seconds' => 'sometimes|integer',
            'type' => 'sometimes|integer',
            'notes' => 'nullable|string',
            'triggers' => 'nullable|array'
        ]);

        $seizure->update($validated);

        if (isset($validated['triggers'])) {
            $seizure->triggers()->sync($validated['triggers']);
        }

        return response()->json($seizure->load('triggers'));
    }

    public function destroy(Seizure $seizure)
    {
        $seizure->delete();
        return response()->json(null, 204);
    }
}

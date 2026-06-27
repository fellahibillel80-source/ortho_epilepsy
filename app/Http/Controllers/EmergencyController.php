<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seizure;
use App\Models\User;
use Carbon\Carbon;

class EmergencyController extends Controller
{
    // Called by the Patient to trigger an emergency
    public function trigger(Request $request)
    {
        $seizure = Seizure::create([
            'user_id' => $request->user()->id,
            'start_time' => Carbon::now(),
            'duration_seconds' => null, // null means ongoing
            'type' => null,
            'notes' => 'Emergency triggered from panic button',
        ]);

        return response()->json(['message' => 'Emergency triggered', 'seizure_id' => $seizure->id]);
    }

    // Called by the Patient to stop the emergency and save details
    public function stop(Request $request)
    {
        $request->validate([
            'seizure_id' => 'required|exists:seizures,id',
            'duration_seconds' => 'required|integer',
        ]);

        $seizure = Seizure::where('id', $request->seizure_id)
                          ->where('user_id', $request->user()->id)
                          ->firstOrFail();

        $seizure->update([
            'duration_seconds' => $request->duration_seconds,
            'type' => $request->type, // optional
            'notes' => $request->notes, // optional
        ]);

        // If triggers are provided, attach them
        if ($request->has('trigger_ids') && is_array($request->trigger_ids)) {
            $seizure->triggers()->sync($request->trigger_ids);
        }

        return response()->json(['message' => 'Emergency stopped and saved', 'seizure' => $seizure]);
    }

    // Called by the Caregiver every 10 seconds (Polling)
    public function status(Request $request)
    {
        // Find the patient linked to this caregiver
        $patient = User::where('caregiver_id', $request->user()->id)->first();

        if (!$patient) {
            return response()->json(['is_emergency' => false, 'message' => 'No patient linked to this caregiver.']);
        }

        // Check if the patient has an ongoing seizure
        $ongoingSeizure = Seizure::where('user_id', $patient->id)
                                 ->whereNull('duration_seconds')
                                 ->first();

        if ($ongoingSeizure) {
            return response()->json([
                'is_emergency' => true,
                'patient_name' => $patient->name,
                'start_time' => $ongoingSeizure->start_time,
                'seizure_id' => $ongoingSeizure->id,
            ]);
        }

        return response()->json(['is_emergency' => false]);
    }
}

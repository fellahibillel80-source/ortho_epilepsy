<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suggestion;
use App\Models\CognitiveTest;
use App\Models\RehabilitationActivity;

class SuggestionController extends Controller
{
    // Specialist: Propose a suggestion
    public function propose(Request $request)
    {
        $specialist = $request->user();
        if ($specialist->role !== 'specialist') {
            return response()->json(['message' => 'غير مصرح كمختص.'], 403);
        }

        $validated = $request->validate([
            'type' => 'required|string|in:cognitive_test,rehab_activity',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'category_or_function' => 'required|string|max:255',
        ]);

        $suggestion = Suggestion::create([
            'specialist_id' => $specialist->id,
            'type' => $validated['type'],
            'name_ar' => $validated['name_ar'],
            'name_en' => $validated['name_en'],
            'description_ar' => $validated['description_ar'],
            'description_en' => $validated['description_en'],
            'category_or_function' => $validated['category_or_function'],
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'تم إرسال اقتراحك بنجاح إلى مدير النظام العام.',
            'suggestion' => $suggestion
        ], 201);
    }

    // List suggestions (Super Admin lists all, Specialist lists their own)
    public function listSuggestions(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'super_admin') {
            $suggestions = Suggestion::with('specialist')->orderBy('created_at', 'desc')->get();
        } else if ($user->role === 'specialist') {
            $suggestions = Suggestion::where('specialist_id', $user->id)->orderBy('created_at', 'desc')->get();
        } else {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        return response()->json($suggestions);
    }

    // Super Admin: Approve suggestion
    public function approve(Request $request, $id)
    {
        if ($request->user()->role !== 'super_admin') {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $suggestion = Suggestion::findOrFail($id);
        if ($suggestion->status !== 'pending') {
            return response()->json(['message' => 'تم اتخاذ قرار بشأن هذا الاقتراح مسبقاً.'], 400);
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $suggestion->name_en)));

        // Insert into appropriate table based on type
        if ($suggestion->type === 'cognitive_test') {
            CognitiveTest::create([
                'slug' => $slug,
                'name_ar' => $suggestion->name_ar,
                'name_en' => $suggestion->name_en,
                'description_ar' => $suggestion->description_ar,
                'description_en' => $suggestion->description_en,
                'executive_function' => $suggestion->category_or_function,
            ]);
        } else if ($suggestion->type === 'rehab_activity') {
            RehabilitationActivity::create([
                'slug' => $slug,
                'name_ar' => $suggestion->name_ar,
                'name_en' => $suggestion->name_en,
                'description_ar' => $suggestion->description_ar,
                'description_en' => $suggestion->description_en,
                'category' => $suggestion->category_or_function,
            ]);
        }

        $suggestion->update([
            'status' => 'approved'
        ]);

        return response()->json([
            'message' => 'تمت الموافقة على الاقتراح وإضافته لبنك النظام بنجاح.',
            'suggestion' => $suggestion
        ]);
    }

    // Super Admin: Reject suggestion
    public function reject(Request $request, $id)
    {
        if ($request->user()->role !== 'super_admin') {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $suggestion = Suggestion::findOrFail($id);
        if ($suggestion->status !== 'pending') {
            return response()->json(['message' => 'تم اتخاذ قرار بشأن هذا الاقتراح مسبقاً.'], 400);
        }

        $suggestion->update([
            'status' => 'rejected'
        ]);

        return response()->json([
            'message' => 'تم رفض الاقتراح.',
            'suggestion' => $suggestion
        ]);
    }
}

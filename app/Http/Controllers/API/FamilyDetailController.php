<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FamilyDetail;

class FamilyDetailController extends Controller
{
    // List all family details
    public function index()
    {
        return FamilyDetail::all();
    }

    // Store a new family detail
    public function store(Request $request)
    {
        $validated = $request->validate([
            'profile_id' => 'required|exists:profiles,id',
            'father_name' => 'nullable|string|max:150',
            'father_occupation' => 'nullable|string|max:150',
            'mother_name' => 'nullable|string|max:150',
            'mother_occupation' => 'nullable|string|max:150',
            'brothers_unmarried' => 'nullable|integer',
            'brothers_married' => 'nullable|integer',
            'sisters_unmarried' => 'nullable|integer',
            'sisters_married' => 'nullable|integer',
            'family_details' => 'nullable|string|max:555',
        ]);

        return FamilyDetail::create($validated);
    }
    /**
 * Get family details by profile_id
 */
    public function showByProfile($profile_id)
    {
        $familyDetail = FamilyDetail::where('profile_id', $profile_id)->first();

        if (!$familyDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Family details not found for this profile',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $familyDetail,
        ]);
    }


    /**
 * Display the specified resource.
 */
    public function show($id)
    {
        $familyDetail = FamilyDetail::find($id);

        if (!$familyDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Family detail not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $familyDetail,
        ]);
    }


    // Update a family detail
    public function update(Request $request, $id)
    {
        $familyDetail = FamilyDetail::findOrFail($id);

        $validated = $request->validate([
            'profile_id' => 'required|exists:profiles,id',
            'father_name' => 'nullable|string|max:150',
            'father_occupation' => 'nullable|string|max:150',
            'mother_name' => 'nullable|string|max:150',
            'mother_occupation' => 'nullable|string|max:150',
            'brothers_unmarried' => 'nullable|integer',
            'brothers_married' => 'nullable|integer',
            'sisters_unmarried' => 'nullable|integer',
            'sisters_married' => 'nullable|integer',
            'family_details' => 'nullable|string|max:555',
        ]);

        $familyDetail->update($validated);

        return $familyDetail;
    }

    // Delete a family detail
    public function destroy($id)
    {
        $familyDetail = FamilyDetail::findOrFail($id);
        $familyDetail->delete();

        return response()->json(['message' => 'Family detail deleted successfully']);
    }
}

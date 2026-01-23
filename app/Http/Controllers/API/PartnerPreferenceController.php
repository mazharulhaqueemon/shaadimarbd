<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PartnerPreference;

class PartnerPreferenceController extends Controller
{
    // List all partner preferences
    public function index()
    {
        return PartnerPreference::all();
    }

    // Store a new partner preference
    public function store(Request $request)
    {
        $validated = $request->validate([
            'profile_id' => 'required|exists:profiles,id',
            'preferred_age_min' => 'nullable|integer',
            'preferred_age_max' => 'nullable|integer',
            'preferred_height_min' => 'nullable|integer',
            'preferred_height_max' => 'nullable|integer',
            'preferred_religion' => 'nullable|string|max:100',
            'preferred_caste' => 'nullable|string|max:100',
            'preferred_education' => 'nullable|string|max:150',
            'preferred_country' => 'nullable|string|max:100',
            'preferred_profession' => 'nullable|string|max:100',
            'other_expectations' => 'nullable|string',
        ]);

        return PartnerPreference::create($validated);
    }
    // Show preference by profile_id
public function showByProfile($profileId)
{
    return PartnerPreference::where('profile_id', $profileId)->firstOrFail();
}


    // Show a single partner preference
    public function show($id)
    {
        return PartnerPreference::findOrFail($id);
    }

    // Update a partner preference
    public function update(Request $request, $id)
    {
        $preference = PartnerPreference::findOrFail($id);

        $validated = $request->validate([
            'profile_id' => 'required|exists:profiles,id',
            'preferred_age_min' => 'nullable|integer',
            'preferred_age_max' => 'nullable|integer',
            'preferred_height_min' => 'nullable|integer',
            'preferred_height_max' => 'nullable|integer',
            'preferred_religion' => 'nullable|string|max:100',
            'preferred_caste' => 'nullable|string|max:100',
            'preferred_education' => 'nullable|string|max:150',
            'preferred_country' => 'nullable|string|max:100',
            'preferred_profession' => 'nullable|string|max:100',
            'other_expectations' => 'nullable|string',
        ]);

        $preference->update($validated);

        return $preference;
    }

    // Delete a partner preference
    public function destroy($id)
    {
        $preference = PartnerPreference::findOrFail($id);
        $preference->delete();

        return response()->json(['message' => 'Partner preference deleted successfully']);
    }
}

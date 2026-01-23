<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{
    // List all locations
    public function index()
    {
        return Location::all();
    }

    // Store a new location
    public function store(Request $request)
    {
        $validated = $request->validate([
            'profile_id' => 'required|exists:profiles,id',
            'present_address' => 'nullable|string|max:100',
            'permanent_address' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:100',
            'residence_status' => 'nullable|in:citizen,permanent_resident,work_permit,student_visa,other',
            'living_status' => 'nullable|in:renting,owned,with_family,other',
        ]);

        return Location::create($validated);
    }

     public function showByProfile($profileId)
    {
        $location = Location::where('profile_id', $profileId)->first();

        if (!$location) {
            return response()->json(['message' => 'Location not found'], 404);
        }

        return $location;
    }

    // Show a single location
    public function show($id)
    {
        return Location::findOrFail($id);
    }

    // Update a location
    public function update(Request $request, $id)
    {
        $location = Location::findOrFail($id);

        $validated = $request->validate([
            'profile_id' => 'required|exists:profiles,id',
            'present_address' => 'nullable|string|max:100',
            'permanent_address' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:100',
            'residence_status' => 'nullable|in:citizen,permanent_resident,work_permit,student_visa,other',
            'living_status' => 'nullable|in:renting,owned,with_family,other',
        ]);

        $location->update($validated);

        return $location;
    }

    // Delete a location
    public function destroy($id)
    {
        $location = Location::findOrFail($id);
        $location->delete();

        return response()->json(['message' => 'Location deleted successfully']);
    }
}

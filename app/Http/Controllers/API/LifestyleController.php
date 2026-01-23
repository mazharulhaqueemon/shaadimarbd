<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lifestyle;

class LifestyleController extends Controller
{
    // List all lifestyle records
    public function index()
    {
        return Lifestyle::all();
    }

    // Store a new lifestyle record
    public function store(Request $request)
    {
        $validated = $request->validate([
            'profile_id' => 'required|exists:profiles,id',
            'diet' => 'nullable|in:vegetarian,non_vegetarian,vegan,halal,other',
            'smoking' => 'nullable|in:yes,no,occasionally',
            'drinking' => 'nullable|in:yes,no,occasionally',
            'hobbies' => 'nullable|string',
        ]);

        return Lifestyle::create($validated);
    }

    // Show lifestyle by profile_id
    public function showByProfile($profileId)
    {
        return Lifestyle::where('profile_id', $profileId)->firstOrFail();
    }


    // Show a single lifestyle record
    public function show($id)
    {
        return Lifestyle::findOrFail($id);
    }

    // Update a lifestyle record
    public function update(Request $request, $id)
    {
        $lifestyle = Lifestyle::findOrFail($id);

        $validated = $request->validate([
            'profile_id' => 'required|exists:profiles,id',
            'diet' => 'nullable|in:vegetarian,non_vegetarian,vegan,halal,other',
            'smoking' => 'nullable|in:yes,no,occasionally',
            'drinking' => 'nullable|in:yes,no,occasionally',
            'hobbies' => 'nullable|string',
        ]);

        $lifestyle->update($validated);

        return $lifestyle;
    }

    // Delete a lifestyle record
    public function destroy($id)
    {
        $lifestyle = Lifestyle::findOrFail($id);
        $lifestyle->delete();

        return response()->json(['message' => 'Lifestyle record deleted successfully']);
    }
}

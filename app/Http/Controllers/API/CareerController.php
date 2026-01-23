<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Career;

use Illuminate\Http\Request;

class CareerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $validated = $request->validate([
            'profile_id' => 'required|exists:profiles,id',
            'profession' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'annual_income' => 'required|numeric',
        ]);

        return Career::create($validated);
    }


    /**
 * Get career by profile_id
 */
    public function showByProfile($profile_id)
    {
        $career = Career::where('profile_id', $profile_id)->first();

        if (!$career) {
            return response()->json([
                'success' => false,
                'message' => 'Career not found for this profile',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $career,
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Career::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
         $career = Career::findOrFail($id);

        $validated = $request->validate([
            'profile_id' => 'required|exists:profiles,id',
            'profession' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'annual_income' => 'required|numeric',
        ]);

        $career->update($validated);

        return $career;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}//test again

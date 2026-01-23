<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Education;

use Illuminate\Http\Request;

class EducationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Education::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $validated = $request->validate([
            'profile_id' => 'required|exists:profiles,id',
            'Heighets_degree' => 'required|string|max:255',
            'institute_name' => 'required|string|max:255',
            'graduation_year' => 'required|integer',
            'additional_certificates' => 'nullable|string|max:255',
        ]);
         return Education::create($validated);
    }

    public function showByProfile($profile_id)
    {
        $education = Education::where('profile_id', $profile_id)->first();
        if (!$education) {
            return response()->json([
                'success' => false,
                'message' => 'No education record found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $education
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
         return Education::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
         $education = Education::findOrFail($id);

        $validated = $request->validate([
            'profile_id' => 'required|exists:profiles,id',
            'Heighets_degree' => 'required|string|max:255',
            'institute_name' => 'required|string|max:255',
            'graduation_year' => 'required|integer',
            'additional_certificates' => 'nullable|string|max:255',
        ]);

        $education->update($validated);

        return $education;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

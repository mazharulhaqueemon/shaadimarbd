<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Education;
use App\Models\Career;
use App\Models\FamilyDetail;
use App\Models\Location;
use App\Models\Lifestyle;
use App\Models\PartnerPreference;
use App\Models\Photo;

class ProfileController extends Controller
{
    // List all profiles with relations
    public function index()
    {
        $profiles = Profile::with([ 'user',
            'education', 'career', 'familyDetail', 'location',
            'lifestyle', 'partnerPreference'
        ])->get();

        return response()->json($profiles);
    }

    // Show single profile with User_ID
    public function showByUser($user_id)
    {
        $profile = Profile::where('user_id', $user_id)->first();

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profile not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile retrieved successfully',
            'data' => $profile
        ]);
    }



    // Show single profile
    public function show($id)
    {
        $profile = Profile::with('user')->findOrFail($id);
        return response()->json($profile, 200);
    }

    // Create profile with related tables
    public function store(Request $request)
    {
       $validated = $request->validate([
            'user_id'        => 'required|exists:users,id',
            'gender'         => 'required|in:male,female,other',
            'dob'            => 'nullable|date',
            'marital_status' => 'nullable|in:never_married,divorced,widow,separated',
            'height_feet'    => 'nullable|numeric',
            'weight_kg'      => 'nullable|integer',
            'blood_group'    => 'nullable|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
            'mother_tongue'  => 'nullable|string|max:100',
            'religion'       => 'nullable|string|max:100',
            'caste'          => 'nullable|string|max:100',
            'sub_caste'      => 'nullable|string|max:100',
            'bio'            => 'nullable|string',
        ]);

        $profile = Profile::create($validated);

        return response()->json($profile, 201);
    }

    // Update profile with related tables
    public function update(Request $request, $id)
    {
        $profile = Profile::findOrFail($id);

        $validated = $request->validate([
            'gender' => 'sometimes|in:male,female,other',
            'dob' => 'nullable|date',
            'marital_status' => 'nullable|in:never_married,divorced,widow,separated',
            'height_feet' => 'nullable|numeric',
            'weight_kg' => 'nullable|integer',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
            'mother_tongue' => 'nullable|string|max:100',
            'religion' => 'nullable|string|max:100',
            'caste' => 'nullable|string|max:100',
            'sub_caste' => 'nullable|string|max:100',
            'bio' => 'nullable|string',
        ]);

        $profile->update($validated);

        return response()->json($profile, 200);
    }

    // Delete profile and cascade delete related data
    public function destroy($id)
    {

    }

    public function advancedSearch(Request $request)
    {
        $query = Profile::query();

        // -------------------------
        // Basic profile filters
        // -------------------------
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('marital_status')) {
            $query->where('marital_status', $request->marital_status);
        }

        if ($request->filled('age_from') && $request->filled('age_to')) {
            $query->whereBetween('dob', [
                now()->subYears($request->age_to),
                now()->subYears($request->age_from)
            ]);
        }

        if ($request->filled('religion')) {
            $query->where('religion', $request->religion);
        }

        if ($request->filled('caste')) {
            $query->where('caste', $request->caste);
        }

        if ($request->filled('sub_caste')) {
            $query->where('sub_caste', $request->sub_caste);
        }

        // -------------------------
        // Location filters
        // -------------------------
        if ($request->filled('country') || $request->filled('state') || $request->filled('city')) {
            $query->whereHas('location', function ($q) use ($request) {
                if ($request->filled('country')) $q->where('country', $request->country);
                if ($request->filled('state')) $q->where('state', $request->state);
                if ($request->filled('city')) $q->where('city', $request->city);
            });
        }

        // -------------------------
        // Education filters
        // -------------------------
        if ($request->filled('degree') || $request->filled('university') || $request->filled('college')) {
            $query->whereHas('education', function ($q) use ($request) {
                if ($request->filled('degree')) $q->where('highest_degree', $request->degree);
                if ($request->filled('university')) $q->where('university', $request->university);
                if ($request->filled('college')) $q->where('college', $request->college);
            });
        }

        // -------------------------
        // Career filters
        // -------------------------
        if ($request->filled('occupation') || $request->filled('annual_income')) {
            $query->whereHas('career', function ($q) use ($request) {
                if ($request->filled('occupation')) $q->where('occupation', $request->occupation);
                if ($request->filled('annual_income')) $q->where('annual_income', '>=', $request->annual_income);
            });
        }

        // -------------------------
        // Lifestyle filters
        // -------------------------
        if ($request->filled('diet') || $request->filled('smoking') || $request->filled('drinking')) {
            $query->whereHas('lifestyle', function ($q) use ($request) {
                if ($request->filled('diet')) $q->where('diet', $request->diet);
                if ($request->filled('smoking')) $q->where('smoking', $request->smoking);
                if ($request->filled('drinking')) $q->where('drinking', $request->drinking);
            });
        }

        // -------------------------
        // Family details filters
        // -------------------------
        if ($request->filled('family_status') || $request->filled('siblings_count')) {
            $query->whereHas('familyDetail', function ($q) use ($request) {
                if ($request->filled('family_status')) $q->where('family_status', $request->family_status);
                if ($request->filled('siblings_count')) $q->where('siblings_count', $request->siblings_count);
            });
        }

        // -------------------------
        // Partner preferences filters (optional)
        // -------------------------
        if ($request->filled('preferred_religion') || $request->filled('preferred_marital_status')) {
            $query->whereHas('partnerPreference', function ($q) use ($request) {
                if ($request->filled('preferred_religion')) $q->where('religion', $request->preferred_religion);
                if ($request->filled('preferred_marital_status')) $q->where('marital_status', $request->preferred_marital_status);
            });
        }

        // -------------------------
        // Eager load relationships
        // -------------------------
        $profiles = $query->with([
            'location',
            'education',
            'career',
            'lifestyle',
            'familyDetail',
            'partnerPreference'
        ])->paginate(20);

        if ($profiles->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No profiles found',
                'data' => []
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profiles retrieved successfully',
            'data' => $profiles
        ]);



    }


    public function getFullUserProfile($userId)
    {
        try {
            $profile = Profile::with([
                'user',
                'education',
                'career',
                'familyDetail',
                'location',
                'lifestyle',
                'partnerPreference',
                'user.profilePictures' // Assuming photos are linked to user
            ])->where('user_id', $userId)->first();

            if (!$profile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profile not found',
                    'data' => null
                ], 404);
            }

            // Format the photos cleanly
            $photos = [];
            if ($profile->user && $profile->user->profilePictures) {
                $photos = $profile->user->profilePictures->map(function ($photo) {
                    return [
                        'id' => $photo->id,
                        'url' => asset('storage/' . $photo->image_path),
                        'is_primary' => (bool) $photo->is_primary,
                    ];
                });
            }

            $data = [
                'id' => $profile->id,
                'user_id' => $profile->user_id,
                'gender' => $profile->gender,
                'dob' => $profile->dob,
                'religion' => $profile->religion,
                'bio' => $profile->bio,
                'user' => [
                    'id' => $profile->user->id,
                    'name' => $profile->user->name,
                    'email' => $profile->user->email,
                ],
                'education' => $profile->education->first() ?? null,
                'career' => $profile->career->first() ?? null,
                'family_detail' => $profile->familyDetail,
                'location' => $profile->location,
                'lifestyle' => $profile->lifestyle,
                'partner_preference' => $profile->partnerPreference,
                'photos' => $photos,
            ];

            return response()->json([
                'success' => true,
                'message' => 'User profile retrieved successfully',
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }


}

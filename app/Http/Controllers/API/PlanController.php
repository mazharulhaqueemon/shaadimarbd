<?php

// namespace App\Http\Controllers\API;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use App\Models\Plan;

// class PlanController extends Controller
// {
//     // Create a new plan  
//     public function create(Request $request)
//     {
//         $request->validate([
//             'plan_name' => 'required|string|unique:plans,plan_name',
//             'description' => 'nullable|string',
//         ]);

//         $plan = Plan::create([
//             'plan_name' => $request->plan_name,
//             'description' => $request->description ?? null,
//         ]);

//         return response()->json([
//             'success' => true,
//             'plan' => $plan,
//         ]);
//     }

    
//     // Get all plans
//     public function index()
//     {
//         $plans = Plan::all();

//         return response()->json([
//             'success' => true,
//             'plans' => $plans,
//         ]);
//     }
// }

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;

class PlanController extends Controller
{
    // Create a new plan  
    public function create(Request $request)
    {
        $request->validate([
            'plan_name' => 'required|string|unique:plans,plan_name',
            'description' => 'nullable|string',
        ]);

        $plan = Plan::create([
            'plan_name' => $request->plan_name,
            'description' => $request->description ?? null,
        ]);

        return response()->json([
            'success' => true,
            'plan' => $plan,
        ]);
    }

    // Get all plans
    public function index()
    {
        $plans = Plan::all()->map(function ($plan) {
            // Decode features if it's stored as a JSON string
            if (!empty($plan->features) && is_string($plan->features)) {
                $decoded = json_decode($plan->features, true);
                $plan->features = is_array($decoded) ? $decoded : [];
            } else {
                $plan->features = $plan->features ?? [];
            }
            return $plan;
        });

        return response()->json([
            'success' => true,
            'plans' => $plans,
        ]);
    }
}

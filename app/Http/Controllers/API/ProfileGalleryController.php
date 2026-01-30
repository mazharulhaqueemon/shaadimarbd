<?php

namespace App\Http\Controllers\API;
use App\Models\ProfileGalleryImage;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileGalleryController extends Controller
{
    // 📌 Upload gallery images (max 4 total)
    public function upload(Request $request)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $profile = $request->user()->profile;

        if (!$profile) {
            return response()->json([
                'status' => false,
                'message' => 'Profile not found',
            ], 404);
        }

        $existingCount = $profile->galleryImages()->count();
        $newCount = count($request->images);

        if (($existingCount + $newCount) > 4) {
            return response()->json([
                'status' => false,
                'message' => 'You can upload maximum 4 gallery images',
            ], 422);
        }

        $savedImages = [];

        foreach ($request->images as $image) {
            $path = $image->store('profile-gallery', 'public');

            $savedImages[] = $profile->galleryImages()->create([
                'path' => $path,
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Gallery images uploaded successfully',
            'data' => $savedImages,
        ]);
    }

    // 📌 List gallery images
   public function index(Request $request)
{
    $images = $request->user()->profile->galleryImages;

    $images->each(function ($image) {
        $image->path = asset('storage/' . $image->path);
    });

    return response()->json([
        'status' => true,
        'data' => $images,
    ]);
}


    // 📌 Delete gallery image
    public function destroy(Request $request, $id)
    {
        $image = ProfileGalleryImage::where('id', $id)
            ->whereHas('profile', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            })
            ->firstOrFail();

        Storage::disk('public')->delete($image->path);
        $image->delete();

        return response()->json([
            'status' => true,
            'message' => 'Gallery image deleted successfully',
        ]);
    }
}

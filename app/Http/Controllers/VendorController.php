<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Statistic;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $vendors = Vendor::with(['category', 'ratings'])
            ->get()
            ->map(function ($vendor) use ($user) {
                $totalRatings = $vendor->ratings->avg('rating') ?? 0;
                $reviewsCount = $vendor->ratings->count();
                
                $userLikes = $user->likes_array ?? [];
                $isLiked = in_array($vendor->id, $userLikes);

                return [
                    'id' => $vendor->id,
                    'name' => $vendor->name,
                    'location' => $vendor->location,
                    'ratings' => round($totalRatings, 2),
                    'reviews' => $reviewsCount,
                    'price' => $vendor->price,
                    'category_id' => $vendor->category_id,
                    'category_name' => $vendor->category->name,
                    'is_like' => $isLiked,
                    'cover' => $vendor->cover,
                ];
            });

        return response()->json(['records' => $vendors]);
    }

    public function show($id)
    {
        $user = auth()->user();
        $vendor = Vendor::with(['category', 'statistics'])->findOrFail($id);

        // Update view count
        $statistics = $vendor->statistics;
        if ($statistics) {
            $statistics->increment('views');
        }

        $userLikes = $user->likes_array ?? [];
        $isLiked = in_array($vendor->id, $userLikes);

        return response()->json([
            'success' => true,
            'payload' => [
                'id' => $vendor->id,
                'name' => $vendor->name,
                'bio' => $vendor->bio,
                'location' => $vendor->location,
                'location_data' => $vendor->location_data,
                'ratings' => $vendor->ratings,
                'reviews' => $vendor->reviews,
                'price' => $vendor->price,
                'category_id' => $vendor->category_id,
                'category_name' => $vendor->category->name,
                'image_url' => $vendor->cover,
                'is_liked' => $isLiked,
                'total_likes' => $statistics->likes ?? 0,
                'total_views' => $statistics->views ?? 0,
                'total_order' => $statistics->orders ?? 0,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->vendor) {
            return response()->json([
                'success' => false,
                'message' => 'You have your own business already.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'bio' => 'nullable|string',
            'price' => 'nullable|numeric',
            'location_data' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to create vendor. Please complete these following data',
                'errors' => $validator->errors()
            ], 400);
        }

        $vendor = Vendor::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'location' => $request->location,
            'category_id' => $request->category_id,
            'bio' => $request->bio,
            'price' => $request->price,
            'location_data' => $request->location_data,
        ]);

        // Create statistics record
        Statistic::create([
            'business_id' => $vendor->id,
            'views' => 0,
            'likes' => 0,
            'orders' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vendor was created.',
            'data' => $vendor->id
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $vendor = $user->vendor;

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Your business does not exist.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'location' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|exists:categories,id',
            'bio' => 'sometimes|string',
            'price' => 'sometimes|numeric',
            'location_data' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $vendor->update($request->only([
            'name', 'location', 'category_id', 'bio', 'price', 'location_data'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Vendor was updated.'
        ]);
    }

    public function me()
    {
        $user = auth()->user();
        $vendor = $user->vendor;

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'No vendor found for this user.'
            ]);
        }

        return response()->json([
            'success' => true,
            'payload' => [
                'id' => $vendor->id,
                'user_id' => $vendor->user_id,
                'name' => $vendor->name,
                'location' => $vendor->location,
                'location_data' => $vendor->location_data,
                'ratings' => $vendor->ratings,
                'reviews' => $vendor->reviews,
                'price' => $vendor->price,
                'category_id' => $vendor->category_id,
                'category_name' => $vendor->category->name,
                'bio' => $vendor->bio,
                'cover' => $vendor->cover,
            ]
        ]);
    }

    public function search(Request $request)
    {
        $keyword = $request->query('keyword', '');

        $vendors = Vendor::with(['category'])
            ->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                      ->orWhere('location', 'like', "%{$keyword}%")
                      ->orWhereHas('category', function ($q) use ($keyword) {
                          $q->where('name', 'like', "%{$keyword}%");
                      });
            })
            ->get()
            ->map(function ($vendor) {
                return [
                    'id' => $vendor->id,
                    'name' => $vendor->name,
                    'location' => $vendor->location,
                    'ratings' => $vendor->ratings,
                    'reviews' => $vendor->reviews,
                    'price' => $vendor->price,
                    'category_id' => $vendor->category_id,
                    'category_name' => $vendor->category->name,
                    'cover' => $vendor->cover,
                ];
            });

        if ($vendors->count() > 0) {
            return response()->json([
                'success' => true,
                'payload' => $vendors
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No vendors found.'
        ]);
    }

    public function uploadCover(Request $request)
    {
        $user = auth()->user();
        $vendor = $user->vendor;

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Your business does not exist.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'base64' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'No image has been attached.'
            ]);
        }

        // Process base64 image
        $base64Data = $request->base64;
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Data));
        
        $fileName = 'cover-' . $vendor->id . '.jpg';
        $filePath = 'images/uploads/' . $fileName;
        
        Storage::disk('public')->put($filePath, $imageData);
        
        $fileUrl = config('app.url') . '/storage/' . $filePath;
        
        $vendor->update(['cover' => $fileUrl]);

        return response()->json([
            'success' => true,
            'message' => 'Cover vendor was updated.'
        ]);
    }
}
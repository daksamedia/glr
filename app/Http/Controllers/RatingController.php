<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RatingController extends Controller
{
    public function show(Request $request)
    {
        $vendorId = $request->query('vendor_id');
        $type = $request->query('type', 'vendor');

        if (!$vendorId) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor ID is required.'
            ]);
        }

        $ratings = Rating::with('user')
            ->where('vendor_id', $vendorId)
            ->where('type', $type)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($rating) {
                return [
                    'id' => $rating->id,
                    'user_id' => $rating->user_id,
                    'vendor_id' => $rating->vendor_id,
                    'comments' => $rating->comments,
                    'rating' => $rating->rating,
                    'firstname' => $rating->user->firstname,
                    'lastname' => $rating->user->lastname,
                    'avatar' => $rating->user->avatar,
                ];
            });

        if ($ratings->count() > 0) {
            return response()->json([
                'success' => true,
                'payload' => $ratings
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No ratings found.'
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required|exists:vendors,id',
            'rating' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string',
            'type' => 'required|in:vendor,venue',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $user = auth()->user();

        Rating::create([
            'vendor_id' => $request->vendor_id,
            'user_id' => $user->id,
            'rating' => $request->rating,
            'comments' => $request->comments,
            'type' => $request->type,
        ]);

        // Update vendor ratings
        $this->updateVendorRatings($request->vendor_id);

        return response()->json([
            'success' => true,
            'message' => 'Rating was added.'
        ]);
    }

    public function myRatings()
    {
        $user = auth()->user();

        $ratings = Rating::with(['vendor.category'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($rating) {
                return [
                    'id' => $rating->id,
                    'vendor_id' => $rating->vendor_id,
                    'vendor_data' => [
                        'id' => $rating->vendor->id,
                        'name' => $rating->vendor->name,
                        'cover' => $rating->vendor->cover,
                        'category_name' => $rating->vendor->category->name,
                        'location' => $rating->vendor->location,
                    ],
                    'comments' => $rating->comments,
                    'rating' => $rating->rating,
                    'created' => $rating->created_at,
                ];
            });

        if ($ratings->count() > 0) {
            return response()->json([
                'success' => true,
                'payload' => $ratings
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No ratings found.'
        ]);
    }

    private function updateVendorRatings($vendorId)
    {
        $ratings = Rating::where('vendor_id', $vendorId)
            ->where('type', 'vendor')
            ->get();

        $averageRating = $ratings->avg('rating');
        $reviewsCount = $ratings->count();

        Vendor::where('id', $vendorId)->update([
            'ratings' => round($averageRating, 2),
            'reviews' => $reviewsCount,
        ]);
    }
}
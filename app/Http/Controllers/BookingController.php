<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|exists:vendors,id',
            'booking_time' => 'required|array',
            'service_id' => 'nullable|exists:services,id',
            'user_data' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $user = auth()->user();
        
        if (!$user && !$request->user_data) {
            return response()->json([
                'success' => false,
                'message' => 'User ID or User Data is required.'
            ]);
        }

        $booking = Booking::create([
            'user_id' => $user ? $user->id : null,
            'user_data' => $request->user_data,
            'business_id' => $request->business_id,
            'service_id' => $request->service_id,
            'booking_time' => $request->booking_time,
            'status' => 'PENDING',
            'expired_date' => now()->addDays(2),
        ]);

        // Send notification email to business owner
        $vendor = Vendor::with('user')->find($request->business_id);
        $this->sendBookingNotification($vendor->user, $booking);

        return response()->json([
            'success' => true,
            'message' => 'Booking was created.'
        ]);
    }

    public function myBookings()
    {
        $user = auth()->user();
        
        $bookings = Booking::with(['vendor.category', 'service'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'business_id' => $booking->business_id,
                    'business_data' => [
                        'id' => $booking->vendor->id,
                        'name' => $booking->vendor->name,
                        'cover' => $booking->vendor->cover,
                        'category_name' => $booking->vendor->category->name,
                        'location' => $booking->vendor->location,
                    ],
                    'service_id' => $booking->service_id,
                    'booking_time' => $booking->booking_time,
                    'status' => $booking->status,
                    'modified' => $booking->updated_at,
                    'created' => $booking->created_at,
                ];
            });

        if ($bookings->count() > 0) {
            return response()->json([
                'success' => true,
                'payload' => $bookings
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No bookings found.'
        ]);
    }

    public function vendorBookings()
    {
        $user = auth()->user();
        $vendor = $user->vendor;

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'No vendor found for this user.'
            ]);
        }

        $bookings = Booking::with(['user'])
            ->where('business_id', $vendor->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($booking) {
                $userData = $booking->user ? [
                    'id' => $booking->user->id,
                    'email' => $booking->user->email,
                    'name' => $booking->user->full_name,
                    'phone' => $booking->user->phone,
                    'address' => $booking->user->address,
                ] : $booking->user_data;

                return [
                    'id' => $booking->id,
                    'business_id' => $booking->business_id,
                    'service_id' => $booking->service_id,
                    'user_data' => $userData,
                    'booking_time' => $booking->booking_time,
                    'status' => $booking->status,
                    'modified' => $booking->updated_at,
                    'created' => $booking->created_at,
                ];
            });

        if ($bookings->count() > 0) {
            return response()->json([
                'success' => true,
                'payload' => $bookings
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No bookings found.'
        ]);
    }

    public function show($id)
    {
        $user = auth()->user();
        $booking = Booking::with(['vendor.category', 'service'])->findOrFail($id);

        // Check if user owns this booking
        if ($booking->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'payload' => [
                'id' => $booking->id,
                'business_id' => $booking->business_id,
                'business_data' => [
                    'id' => $booking->vendor->id,
                    'name' => $booking->vendor->name,
                    'cover' => $booking->vendor->cover,
                    'category_name' => $booking->vendor->category->name,
                    'location' => $booking->vendor->location,
                ],
                'service_id' => $booking->service_id,
                'service' => $booking->service ? [
                    'id' => $booking->service->id,
                    'title' => $booking->service->title,
                    'description' => $booking->service->description,
                    'price' => $booking->service->price,
                    'image' => $booking->service->image,
                ] : null,
                'booking_time' => $booking->booking_time,
                'status' => $booking->status,
                'notes' => $booking->notes,
                'created' => $booking->created_at,
                'modified' => $booking->updated_at,
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $booking = Booking::findOrFail($id);

        // Check authorization
        if ($booking->user_id !== $user->id && $booking->vendor->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:PENDING,CONFIRMED,CANCELLED,EXPIRED',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        if (in_array($request->status, ['CANCELLED', 'CANCEL']) && !$request->notes) {
            return response()->json([
                'success' => false,
                'message' => 'You need a reason for cancellation.'
            ]);
        }

        $booking->update([
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking has been updated.'
        ]);
    }

    private function sendBookingNotification($user, $booking)
    {
        // Email notification logic here
        // You can implement this using Laravel's Mail facade
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $vendor = $user->vendor;

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Business does not exist. Please create it first.'
            ]);
        }

        $services = $vendor->services->map(function ($service) {
            return [
                'id' => $service->id,
                'title' => $service->title,
                'description' => $service->description,
                'image' => $service->image,
                'price' => $service->price,
                'created' => $service->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'payload' => $services
        ]);
    }

    public function show(Request $request)
    {
        $vendorId = $request->query('vendor_id');
        
        if (!$vendorId) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor ID is required.'
            ]);
        }

        $services = Service::where('vendor_id', $vendorId)->get()->map(function ($service) {
            return [
                'id' => $service->id,
                'title' => $service->title,
                'description' => $service->description,
                'image' => $service->image,
                'price' => $service->price,
                'created' => $service->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'payload' => $services
        ]);
    }

    public function detail(Request $request)
    {
        $serviceId = $request->query('id');
        $service = Service::findOrFail($serviceId);

        return response()->json([
            'success' => true,
            'payload' => [
                'id' => $service->id,
                'title' => $service->title,
                'description' => $service->description,
                'price' => $service->price,
                'image' => $service->image,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $vendor = $user->vendor;

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Business does not exist. Please create it first.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'image' => 'nullable|string',
            'base64' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $imageUrl = $request->image;

        if ($request->base64) {
            $base64Data = $request->base64;
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Data));
            
            $fileName = 'service-' . $vendor->id . '-' . rand(10, 10000) . '.jpg';
            $filePath = 'images/uploads/' . $fileName;
            
            Storage::disk('public')->put($filePath, $imageData);
            $imageUrl = config('app.url') . '/storage/' . $filePath;
        }

        Service::create([
            'vendor_id' => $vendor->id,
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imageUrl,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Service was created.'
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:services,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric',
            'image' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $service = Service::findOrFail($request->id);
        
        // Check if user owns this service
        $user = auth()->user();
        if ($service->vendor->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        $service->update($request->only(['title', 'description', 'price', 'image']));

        return response()->json([
            'success' => true,
            'message' => 'Service was updated.'
        ]);
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:services,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $service = Service::findOrFail($request->id);
        
        // Check if user owns this service
        $user = auth()->user();
        if ($service->vendor->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service was removed.'
        ]);
    }
}
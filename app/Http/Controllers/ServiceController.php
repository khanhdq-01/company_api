<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $service = Service::all();
        return response()->json($service);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceRequest $request)
    {
        $imageName = null;
            if ($request->file('image')) {
                $file = $request->file('image');
                $fileName = $file->getClientOriginalName();
                
                // Store the image in the public disk under the 'services' folder
                Storage::disk('public')->putFileAs('services', $file, $fileName);
                $imageName = $fileName;
            }

        $service = Service::create([
            'title' => $request->title,
            'long_description' => $request->long_description,
            'full_description' => $request->full_description,
            'image' => $imageName,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Service create successfully!',
            'data' => $service,
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceRequest $request, $id)
    {
        if ($request->isMethod('POST') && $request->has('_method')) {
            $request->setMethod($request->input('_method'));
        }

        $service = Service::with('user')->where('id', $id)->first();
        if (!$service) {
            return response()->json([
                'message' => 'Service not found',
            ], 404);
        }

        if ($service->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Access denied',
            ], 403);
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = $file->getClientOriginalName();

            // Lưu ảnh mới
            Storage::disk('public')->putFileAs('services', $file, $fileName);
            // Cập nhật tên ảnh mới
            $service->image = $fileName;
        }

        // Cập nhật các trường khác
        $service->title = $request->input('title');
        $service->long_description = $request->input('long_description');
        $service->full_description = $request->input('full_description');
        $service->save();

        return response()->json([
            'message' => 'Service updated successfully',
            'data' => $service
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $service = Service::findOrFail($id);

        if (!$service) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $service->delete();
        return response()->json([
            'message' => 'Service deleted successfully',
            'data' => $service
        ]);
    }

    public function updateStatus(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive'
        ]);

        $service = Service::findOrFail($id);
        $service->status = $request->status;
        $service->save();
        return response()->json([
            'message'=> 'Update status success',
            'data'=> $service
        ], 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with('user')->where('status', 'active')->paginate(6);
        return response()->json($services);
    }

    public function store(ServiceRequest $request)
    {
        $imageName = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
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
            'message' => 'Service created successfully!',
            'data' => $service,
        ], 201);
    }

    public function show($id)
    {
        $service = Service::with('user')->findOrFail($id);

        return response()->json([
            'message' => 'Service detail fetched successfully',
            'data' => $service,
        ]);
    }

    public function update(ServiceRequest $request, $id)
    {
        if ($request->isMethod('POST') && $request->has('_method')) {
            $request->setMethod($request->input('_method'));
        }

        $service = Service::findOrFail($id);

        $service->title = $request->title;
        $service->long_description = $request->long_description;
        $service->full_description = $request->full_description;


        if ($request->hasFile('image')) {
            if ($service->image) {
                Storage::disk('public')->delete('services/' . $service->image);
            }

            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            Storage::disk('public')->putFileAs('services', $file, $fileName);
            $service->image = $fileName;
        }

        $service->save();

        return response()->json([
            'message' => 'Service updated successfully',
            'data' => $service->load('user')
        ], 200);
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);

        if ($service->image) {
            Storage::disk('public')->delete('services/' . $service->image);
        }

        $service->delete();

        return response()->json([
            'message' => 'Service deleted successfully',
            'data' => $service
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive'
        ]);

        $service = Service::findOrFail($id);
        $service->status = $request->status;
        $service->save();

        return response()->json([
            'message' => 'Status updated successfully',
            'data' => $service
        ], 200);
    }

    public function uploadCKEditorImage(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->storeAs('public/service_images', $filename);
            $url = url('storage/service_images/'.$filename);
            return response()->json([
                'uploaded' => 1,
                'fileName' => $filename,
                'url' => $url,
            ]);
        }
        return response()->json(['uploaded' => 0, 'error' => ['message' => 'No file uploaded.']]);
    }
}

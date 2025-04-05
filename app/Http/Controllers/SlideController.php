<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slide;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class SlideController extends Controller
{
    public function store(Request $request) {
        $request->validate([
            'title' => 'required',
            'description' => 'required|string',
            'image_path' => 'nullable|mimes:jpg,png',
        ]);

        $imageName = null;
        if ($request->file('image_path')) {
            $file = $request->file('image_path');
            $fileName = $file->getClientOriginalName();
            $newName = Carbon::now()->timestamp . '_' . $fileName;
            
            // Store the image in the public disk under the 'slides' folder
            Storage::disk('public')->putFileAs('slides', $file, $newName);
            $imageName = $newName;
        }
    
        // Create the slide and store only relevant fields
        $slide = Slide::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'image_path' => $imageName,  // Store the image file name if it exists
        ]);
    
        return response(['data' => $slide], 201);  // Added status code for resource creation
    }
    
    

    public function index() {
        $slides = Slide::select('id', 'title','description', 'image_path')->get();
        return response(['data'=> $slides]);
    }

    public function show($id)
    {
        $slides = Slide::findOrFail($id);
        return response()->json($slides);
    }

    public function update(Request $request, $id) {
        if ($request->isMethod('POST') && $request->has('_method')) {
            $request->setMethod($request->input('_method'));
        }
        // Validate request
        $request->validate([
            'title' => 'required|max:100',
            'description' => 'required|string',
            'image_path' => 'nullable|mimes:jpg,png|max:2048',
        ]);

        // Tìm dữ liệu
        $slide = Slide::findOrFail($id);
        $dataToUpdate = $request->only(['title', 'description', 'image_path']);
    
        // Xử lý file ảnh
        if ($request->hasFile('image_path')) {
            $file = $request->file('image_path');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath =  $fileName;
    
            // Lưu file vào storage
            Storage::disk('public')->putFileAs('slides', $file, $fileName);
            $dataToUpdate['image_path'] = $filePath;
        }
    
        // Cập nhật dữ liệu
        $slide->update($dataToUpdate);
    
        return response(['data' => $slide], 200);
    }

    public function destroy($id)
    {
        $slide = Slide::findOrFail($id);

        if (!$slide) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $slide->delete();
        return response()->json([
            'message' => 'Order deleted successfully',
            'data' => $slide
        ]);
    }

}

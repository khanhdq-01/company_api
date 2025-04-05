<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\About;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AboutController extends Controller
{
    public function store(Request $request) {
        $request->validate([
            'subtitle' => 'required|string',
            'title' => 'required|string',
            'content' => 'required|string',
            'description' => 'required|string',
            'image_path' => 'nullable|mimes:jpg,png',
        ]);

        $imageName = null;
        if ($request->file('image_path')) {
            $file = $request->file('image_path');
            $fileName = $file->getClientOriginalName();
            $newName = Carbon::now()->timestamp . '_' . $fileName;
            
            // Store the image in the public disk under the 'abouts' folder
            Storage::disk('public')->putFileAs('abouts', $file, $newName);
            $imageName = $newName;
        }
    
        // Create the about and store only relevant fields
        $about = About::create([
            'subtitle' => $request->input('subtitle'),
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'description' => $request->input('description'),
            'image_path' => $imageName,
        ]);
    
        return response(['data' => $about], 201);  // Added status code for resource creation
    }
    
    

    public function index() {
        $abouts = About::select('id','subtitle', 'title','content','description', 'image_path')->get();
        return response(['data'=> $abouts]);
    }

    public function show($id)
    {
        $abouts = About::findOrFail($id);
        return response()->json($abouts);
    }

    public function update(Request $request, $id) {
        if ($request->isMethod('POST') && $request->has('_method')) {
            $request->setMethod($request->input('_method'));
        }
        // Validate request
        $request->validate([
            'subtitle' => 'required|string',
            'title' => 'required|string',
            'content' => 'required|string',
            'description' => 'required|string',
            'image_path' => 'nullable|mimes:jpg,png|max:2048',
        ]);

        // Tìm dữ liệu
        $about = About::findOrFail($id);
        $dataToUpdate = $request->only(['subtitle','title','content','description','image_path']);
    
        // Xử lý file ảnh
        if ($request->hasFile('image_path')) {
            $file = $request->file('image_path');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath =  $fileName;
    
            // Lưu file vào storage
            Storage::disk('public')->putFileAs('abouts', $file, $fileName);
            $dataToUpdate['image_path'] = $filePath;
        }
    
        // Cập nhật dữ liệu
        $about->update($dataToUpdate);
    
        return response(['data' => $about], 200);
    }

    public function destroy($id)
    {
        $about = About::findOrFail($id);

        if (!$about) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $about->delete();
        return response()->json([
            'message' => 'Order deleted successfully',
            'data' => $about
        ]);
    }

}

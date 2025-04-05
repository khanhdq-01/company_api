<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|max:100',
            'description' => 'required|max:300',
            'image' => 'nullable|mimes:jpg,png',
        ]);

        $imageName = null;
        if ($request->file('image')) {
            $file = $request->file('image');
            $fileName = $file->getClientOriginalName();
            $newName = Carbon::now()->timestamp . '_' . $fileName;
            
            // Store the image in the public disk under the 'blogs' folder
            Storage::disk('public')->putFileAs('blogs', $file, $newName);
            $imageName = $newName;
        }
    
        // Create the blogs and store only relevant fields
        $blog = Blog::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'image' => $imageName,  // Store the image file name if it exists
        ]);
    
        return response(['data' => $blog], 201);  // Added status code for resource creation
    }
    
    

    public function index() {
        $blogs = Blog::select('id', 'name','description', 'image')->get();
        return response(['data'=> $blogs]);
    }

    public function show($id)
    {
        $blogs = Blog::findOrFail($id);
        return response()->json($blogs);
    }

    public function update(Request $request, $id) {
        if ($request->isMethod('POST') && $request->has('_method')) {
            $request->setMethod($request->input('_method'));
        }
        // Validate request
        $request->validate([
            'name' => 'required|max:100',
            'description' => 'required|max:300',
            'image' => 'nullable|mimes:jpg,png|max:2048',
        ]);

        // Tìm dữ liệu
        $blog = Blog::findOrFail($id);
        $dataToUpdate = $request->only(['name','description', 'image']);
    
        // Xử lý file ảnh
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath =  $fileName;
    
            // Lưu file vào storage
            Storage::disk('public')->putFileAs('blogs', $file, $fileName);
            $dataToUpdate['image'] = $filePath;
        }
    
        // Cập nhật dữ liệu
        $blog->update($dataToUpdate);
    
        return response(['data' => $blog], 200);
    }

    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);

        if (!$blog) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $blog->delete();
        return response()->json([
            'message' => 'Order deleted successfully',
            'data' => $blog
        ]);
    }

}

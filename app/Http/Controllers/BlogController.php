<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use File;

class BlogController extends Controller
{
    public function store(Request $request) 
    {
        try {
            $request->validate([
                'title' => 'required',
                'name' => 'required|max:100',
                'description' => 'required',
                'image' => 'nullable|mimes:jpg,png',
                'user_id' => 'required',
            ]);
    
            $imageName = null;
            if ($request->file('image')) {
                $file = $request->file('image');
                $fileName = $file->getClientOriginalName();
                
                // Store the image in the public disk under the 'blogs' folder
                Storage::disk('public')->putFileAs('blogs', $file, $fileName);
                $imageName = $fileName;
            }
    
            // Create the blogs and store only relevant fields
            $blog = Blog::create([
                'title' => $request->input('title'),
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'image' => $imageName,  // Store the image file name if it exists
                'user_id' => auth()->id(),
            ]);
        
            return response(['data' => $blog], 201);  // Added status code for resource creation
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            if ($request->isMethod('POST') && $request->has('_method')) {
                $request->setMethod($request->input('_method'));
            }

            // Tìm bài viết cùng với thông tin người dùng
            $blog = Blog::with('user')->where('id', $id)->first();

            if (!$blog) {
                return response()->json([
                    'message' => 'Blog not found',
                ], 404);
            }

            // Kiểm tra quyền người dùng (người tạo blog mới được phép cập nhật)
            if ($blog->user_id !== $request->user()->id) {
                return response()->json([
                    'message' => 'Access denied',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'name' => 'required|string|max:100',
                'description' => 'required|string',
                'image' => 'nullable|image|mimes:jpg,png|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation error',
                    'errors' => $validator->messages(),
                ], 422);
            }

            // Xử lý ảnh nếu có
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = $file->getClientOriginalName();

                // Lưu ảnh mới
                Storage::disk('public')->putFileAs('blogs', $file, $fileName);
                // Cập nhật tên ảnh mới
                $blog->image = $fileName;
            }

            // Cập nhật các trường khác
            $blog->title = $request->input('title');
            $blog->name = $request->input('name');
            $blog->description = $request->input('description');
            $blog->save();

            return response()->json([
                'message' => 'Blog updated successfully',
                'data' => $blog
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
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

    public function details()
    {
        $blog = Blog::with('user')->paginate(6);
        if ($blog) {
            return response()->json([
                'message'=> 'Blog successfully fetched',
                'data'=> $blog
            ],200);
        } else {
            return response()->json([
                'message'=> 'No blog found',
            ],404);
        }
    }

    public function uploadCKEditorImage(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->storeAs('public/blog_images', $filename);
            $url = url('storage/blog_images/'.$filename);
            return response()->json([
                'uploaded' => 1,
                'fileName' => $filename,
                'url' => $url,
            ]);
        }
        return response()->json(['uploaded' => 0, 'error' => ['message' => 'No file uploaded.']]);
    }
}

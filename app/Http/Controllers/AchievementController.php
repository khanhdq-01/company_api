<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class AchievementController extends Controller
{
    public function index()
    {
        $achievement = Achievement::select('id','title','category', 'date', 'summary', 'description', 'image_path')->get();
        return response(['data'=> $achievement]);
    }
    public function show($id)
    {
        $achievement = Achievement::find($id);

        if (!$achievement) {
            return response()->json(['message' => 'Không tìm thấy thành tựu'], 404);
        }

        return response()->json($achievement);
    }


    public function store(Request $request)
    {
        
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'date' => 'required|date',
            'summary' => 'required|string|max:500',
            'description' => 'nullable|string',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
       
        $imageName = null;
        if ($request->file('image_path')) {
            $file = $request->file('image_path');
            $fileName = $file->getClientOriginalName();
            $newName = Carbon::now()->timestamp . '_' . $fileName;
            
            // Store the image in the public disk under the 'companys' folder
            Storage::disk('public')->putFileAs('achievements', $file, $newName);
            $imageName = $newName;
        }
    
        $achievement = Achievement::create([
            'title' => $request->input('title'),
            'category' => $request->input('category'),
            'date' => $request->input('date'),
            'summary' => $request->input('summary'),
            'description' => $request->input('description'),
            'image_path' => $imageName, 
        ]);

        return response(['data' => $achievement], 201);
    }

    public function update(Request $request, $id)
    {
        if ($request->isMethod('POST') && $request->has('_method')) {
            $request->setMethod($request->input('_method'));
        }
        
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'category' => 'sometimes|string|max:100',
            'date' => 'sometimes|date',
            'summary' => 'sometimes|string|max:500',
            'description' => 'nullable|string',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $achievement = Achievement::findOrFail($id);

        $dataToUpdate = $request->only(['title','category', 'date', 'summary', 'description','image_path']);

        if ($request->hasFile('image_path')) {
            $file = $request->file('image_path');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath =  $fileName;
    
            // Lưu file vào storage
            Storage::disk('public')->putFileAs('achievements', $file, $fileName);
            $dataToUpdate['image_path'] = $filePath;
        }
        
        $achievement->update($dataToUpdate);
        return response(['data' => $achievement], 200);

    }

    public function destroy($id)
    {
        $achievement = Achievement::findOrFail($id);

        if (!$achievement) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $achievement->delete();

        return response()->json([
            'message' => 'Thành tựu đã được xóa',
            'data' => $achievement
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MemberOther;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class MemberOtherController extends Controller
{
    public function store(Request $request) {
        $request->validate([
            'name' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'image_path' => 'sometimes|nullable|mimes:jpg,png',
        ]);

        $imageName = null;
        if ($request->file('image_path')) {
            $file = $request->file('image_path');
            $fileName = $file->getClientOriginalName();
            $newName = Carbon::now()->timestamp . '_' . $fileName;
            
            // Store the image in the public disk under the 'member_others' folder
            Storage::disk('public')->putFileAs('member_others', $file, $newName);
            $imageName = $newName;
        }
    
        // Create the member_others and store only relevant fields
        $memberOther = MemberOther::create([
            'name' => $request->input('name'),
            'position' => $request->input('position'),
            'image_path' => $imageName,  // Store the image file name if it exists
        ]);
    
        return response(['data' => $memberOther], 201);  // Added status code for resource creation
    }
    
    

    public function index() {
        $memberOther = MemberOther::select('id', 'name','position', 'image_path')->get();
        return response(['data'=> $memberOther]);
    }

    public function show($id)
    {
        $memberOther = MemberOther::findOrFail($id);
        return response()->json($memberOther);
    }

    public function update(Request $request, $id) {
        if ($request->isMethod('POST') && $request->has('_method')) {
            $request->setMethod($request->input('_method'));
        }
        // Validate request
        $request->validate([
            'name' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'image_path' => 'sometimes|nullable|mimes:jpg,png',
        ]);
        
        // Tìm dữ liệu
        $memberOther = MemberOther::findOrFail($id);
        $dataToUpdate = $request->only(['name','position', 'image_path']);
    
        // Xử lý file ảnh
        if ($request->hasFile('image_path')) {
            $file = $request->file('image_path');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath =  $fileName;
    
            // Lưu file vào storage
            Storage::disk('public')->putFileAs('member_others', $file, $fileName);
            $dataToUpdate['image_path'] = $filePath;
        }
    
        // Cập nhật dữ liệu
        $memberOther->update($dataToUpdate);
    
        return response(['data' => $memberOther], 200);
    }

    public function destroy($id)
    {
        $memberOther = MemberOther::findOrFail($id);

        if (!$memberOther) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $memberOther->delete();
        return response()->json([
            'message' => 'Order deleted successfully',
            'data' => $memberOther
        ]);
    }

}

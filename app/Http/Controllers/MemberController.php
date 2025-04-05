<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|max:100',
            'position' => 'required|max:100',
            'description' => 'required|max:100',
            'image_path' => 'nullable|mimes:jpg,png',
        ]);

        $imageName = null;
        if ($request->file('image_path')) {
            $file = $request->file('image_path');
            $fileName = $file->getClientOriginalName();
            $newName = Carbon::now()->timestamp . '_' . $fileName;
            
            // Store the image in the public disk under the 'members' folder
            Storage::disk('public')->putFileAs('members', $file, $newName);
            $imageName = $newName;
        }
    
        // Create the member and store only relevant fields
        $member = Member::create([
            'name' => $request->input('name'),
            'position' => $request->input('position'),
            'description' => $request->input('description'),
            'image_path' => $imageName,  // Store the image file name if it exists
        ]);
    
        return response(['data' => $member], 201);  // Added status code for resource creation
    }
    
    

    public function index() {
        $members = Member::select('id', 'name','position', 'description', 'image_path')->get();
        return response(['data'=> $members]);
    }

    public function show($id)
    {
        $members = Member::findOrFail($id);
        return response()->json($members);
    }

    public function update(Request $request, $id) {
        if ($request->isMethod('POST') && $request->has('_method')) {
            $request->setMethod($request->input('_method'));
        }
        // Validate request
        $request->validate([
            'name' => 'required|max:100',
            'position' => 'required|max:100',
            'description' => 'required|max:100',
            'image_path' => 'nullable|mimes:jpg,png',
        ]);

        // Tìm dữ liệu
        $member = Member::findOrFail($id);
        $dataToUpdate = $request->only(['name','position', 'description', 'image_path']);
    
        // Xử lý file ảnh
        if ($request->hasFile('image_path')) {
            $file = $request->file('image_path');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath =  $fileName;
    
            // Lưu file vào storage
            Storage::disk('public')->putFileAs('members', $file, $fileName);
            $dataToUpdate['image_path'] = $filePath;
        }
    
        // Cập nhật dữ liệu
        $member->update($dataToUpdate);
    
        return response(['data' => $member], 200);
    }

    public function destroy($id)
    {
        $member = Member::findOrFail($id);

        if (!$member) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $member->delete();
        return response()->json([
            'message' => 'Order deleted successfully',
            'data' => $member
        ]);
    }

}

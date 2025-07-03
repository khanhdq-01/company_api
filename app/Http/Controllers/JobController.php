<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    // Lấy danh sách tất cả công việc
    public function index()
    {
        return response()->json(Job::all(), 200);
    }

    // Tạo một công việc mới
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric|min:0',
        ]);

        $job = Job::create($validated);
        return response()->json($job, 201);
    }

    // Lấy chi tiết công việc theo ID
    public function show($id)
    {
        $job = Job::findOrFail($id);
        return response()->json($job, 200);
    }

    // Cập nhật công việc theo ID
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'location' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric|min:0',
        ]);

        $job = Job::findOrFail($id);
        $job->update($validated);

        return response()->json($job, 200);
    }

    // Xoá công việc theo ID
    public function destroy($id)
    {
        $job = Job::findOrFail($id);
        $job->delete();

        return response()->json(null, 204);
    }
}

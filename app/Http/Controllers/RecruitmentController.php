<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecruitmentRequest;
use App\Models\Recruitment;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RecruitmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $recruiments = Recruitment::all();
        return response(['data' => $recruiments]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RecruitmentRequest $request)
    {
        $recruiments = Recruitment::create([
            'position' => $request->position,
            'department' => $request->department,
            'quantity' => $request->quantity,
            'job_type' => $request->job_type,
            'start_date' => $request->start_date,
            'deadline' => $request->deadline,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);
        
        return response()->json($recruiments, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $recruiments = Recruitment::with('user')->findOrFail($id);

        return response()->json([
            'message' => 'Recruments detail fetched successfully',
            'data' => $recruiments
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RecruitmentRequest $request, string $id)
    {
        $recruiments = Recruitment::findOrFail($id);
        $recruiments->update([
            'position' => $request->position,
            'department' => $request->department,
            'quantity' => $request->quantity,
            'job_type' => $request->job_type,
            'start_date' => $request->start_date,
            'deadline' => $request->deadline,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Recruments update fetched successfully',
            'data' => $recruiments
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $recruiments = Recruitment::findOrFail($id);
        $recruiments->delete();

        return response()->json([
            'message' => 'Recruments delete fetched successfully',
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:open,closed,pending'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }

        $recruitment = Recruitment::findOrFail($id);
        $recruitment->status = $validated['status'];
        $recruitment->save();

        return response()->json([
            'message' => 'Status updated successfully.',
            'data' => $recruitment
        ]);
    }
}
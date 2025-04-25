<?php

namespace App\Http\Controllers;

use App\Http\Requests\VideoRequest;
use App\Models\Video;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $video = Video::latest()->get(['id', 'video_url']);
        return response()->json($video);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VideoRequest $request)
    {
        $video = Video::create($request->all());
        return response()->json([
            'message'=> 'Video create success',
            'data'=> $video
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $video = Video::findOrFail($id);
        $video->delete();
        return response()->json([
            'message'=> 'Delete video success',
            'data'=> $video
        ],200);
    }
}

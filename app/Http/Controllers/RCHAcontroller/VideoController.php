<?php

namespace App\Http\Controllers\RCHAcontroller;

// use App\Models\Videos;
use App\Models\FreeVideos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\PaidVideos;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{
      /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeFreeVideos(Request $request)
    {
        // Validate and save video
        try {
            $validator = Validator::make($request->all(), [
                'place_id' => 'required|exists:places,id',
                'self_guided_short_version' => 'required|string',
                'short_eng_version_360_video' => 'required|string',
                'short_french_version_360_video' => 'required|string',
                'short_kiny_version_360_video' => 'required|string',
               
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $video = FreeVideos::create($validator->validated());

            return response()->json([
             'message' => 'Free Video saved successfully',
             'data' => $video], 201);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([ 'message' => 'Something happed while saving Free Videos!'], 501);
        }
    }

    public function storePaidVideos(Request $request)
    {
        // Validate and save video
        try {
            $validator = Validator::make($request->all(), [
                'place_id' => 'required|exists:places,id',
                'long_version_self_guided' => 'required|string',
                'long_eng_version_360_video' => 'required|string',
                'long_french_version_360_video' => 'required|string',
                'long_kiny_version_360_video' => 'required|string',
               
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $video = PaidVideos::create($validator->validated());

            return response()->json([
             'message' => 'Paid Video saved successfully',
             'data' => $video], 201);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([ 'message' => 'Something happed while saving Paid Videos!'], 501);
        }
    }
}




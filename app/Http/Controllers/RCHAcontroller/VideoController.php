<?php

namespace App\Http\Controllers\RCHAcontroller;

// use App\Models\Videos;
use App\Models\Place;
use App\Models\FreeVideos;
use App\Models\PaidVideos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
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
    public function updatePaidVideos(Request $request,$place_id)
    {
      // Validate and update video
      try {
        $PaidVideos = PaidVideos::where('place_id',$place_id);
        if(!$PaidVideos)
       { 
        return response()->json(['message' => 'This place do not have Paid videos!']);
        }
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
        // Update paidVideos
        $PaidVideos->update([
        // 'place_id' => 'required|exists:places,id',
        'long_version_self_guided' => 'required|string',
        'long_eng_version_360_video' => 'required|string',
        'long_french_version_360_video' => 'required|string',
        'long_kiny_version_360_video' => 'required|string',
        ]);
        return response()->json([
         'message' => 'Paid Video Updated successfully',
         'data' => $PaidVideos], 201);
    } catch (\Exception $e) {
        Log::error($e->getMessage());
        return response()->json([ 'message' => 'Something happed while Updating Paid Videos!'], 501);
    }
    }
    public function deletePaidVideos($place_id){
        try {
            $PaidVideos = PaidVideos::where('place_id',$place_id);
            if(!$PaidVideos)
           { 
            return response()->json(['message' => 'This place do not have Paid videos!']);
            }
            $PaidVideos ->delete();
             return response()->json([
                'message' => 'Paid Video Deleted successfully'],201);
           } catch (\Exception $e) {
               Log::error($e->getMessage());
               return response()->json([ 'message' => 'Something happed while Updating Paid Videos!'], 501);
           }

    }

    public function updateFreeVideos(Request $request,$place_id)
    {
      // Validate and update video
      try {
        $FreeVideos = FreeVideos::where('place_id',$place_id);
        if(!$FreeVideos)
       { 
        return response()->json(['message' => 'This place do not have Free videos!']);
        }
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
        // Update paidVideos
        $FreeVideos->update([
        // 'place_id' => 'required|exists:places,id',
       
                'self_guided_short_version' => 'required|string',
                'short_eng_version_360_video' => 'required|string',
                'short_french_version_360_video' => 'required|string',
                'short_kiny_version_360_video' => 'required|string',
        ]);
        return response()->json([
         'message' => 'Free Video Updated successfully',
         'data' => $FreeVideos], 201);
    } catch (\Exception $e) {
        Log::error($e->getMessage());
        return response()->json([ 'message' => 'Something happed while Updating Free Videos!'], 501);
    }
    }
    public function deleteFreeVideos($place_id){
        try {
            $FreeVideos = FreeVideos::where('place_id',$place_id);
            if(!$FreeVideos)
           { 
            return response()->json(['message' => 'This place do not have Free videos!']);
            }
            $FreeVideos ->delete();
             return response()->json([
                'message' => 'Free Video Deleted successfully'],201);
           } catch (\Exception $e) {
               Log::error($e->getMessage());
               return response()->json([ 'message' => 'Something happed while Deleting Free Videos!'], 501);
           }

    }
    public function getPaidVideosForPlace($place_id){
        try {
            $PaidVideos = PaidVideos::where('place_id',$place_id);
            if(!$PaidVideos)
           { 
            return response()->json(['message' => 'This place do not have Paid videos!']);
            }
             return response()->json([
                'PaidVideos' => $PaidVideos],201);
           } catch (\Exception $e) {
               Log::error($e->getMessage());
               return response()->json([ 'message' => 'Something happed while getting Paid Videos For Place chosen!'], 501);
           }

    }
    public function getFreeVideosForPlace($place_id){
        try {
            $FreeVideos = FreeVideos::where('place_id',$place_id);
            if(!$FreeVideos)
           { 
            return response()->json(['message' => 'This place do not have Free videos!']);
            }
             return response()->json([
                'FreeVideos' => $FreeVideos],201);
           } catch (\Exception $e) {
               Log::error($e->getMessage());
               return response()->json([ 'message' => 'Something happed while getting Free Videos For Place chosen!'], 501);
           }

    }
}



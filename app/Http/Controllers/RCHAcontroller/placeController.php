<?php

namespace App\Http\Controllers\RCHAcontroller;

use App\Models\Image;
use App\Models\Place;
use App\Models\FreeVideos;
use App\Models\PaidVideos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class placeController extends Controller
{

    public function storePlace(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'place_name' => 'required',
                'place_location' => 'required',
                'place_status' => 'required',
                'place_details' => 'required',
                'category_id' => 'required',
                // 'place_preview_video' => 'required',
                // 'place_link' => 'required',
                'amount' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $place = Place::create($validator->validated());

            return response()->json([
                'message' => 'Place is recorded successful!',
                'place' => $place
            ], 201);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Exception occurred' . $e->getMessage());

            // Return an error response
            return response()->json(['message' => 'An error occurred while processing your request.'], 501);
        }
    }

    public function getPlaces()
    {
        try {
            $place = Place::all();
            if ($place)
                return response()->json($place, 201);
            return response()->json(['message' => 'Places are not found!'], 405);
        } catch (\Exception $e) {
            Log::error('Exception occurred' . $e->getMessage());
            return response()->json(['message' => 'Something happended while getting places'], 501);
        }
    }
    public function getPlaceById($place_id)
    {
        try {
            $place = Place::find($place_id);
            if ($place === null) {
                return response()->json(['message' => 'Place not found try again!'], 405);
            }
            return response()->json($place, 201);
        } catch (\Exception $e) {
            Log::error('Exception occurred' . $e->getMessage());
            return response()->json(['message' => 'Something happended while getting place id'], 501);
        }
    }

    public function updatePlace(Request $request, $place_id)
    {
        //$user = JWTAuth::parseToken()->authenticate(); // Get the authenticated user using JWT
        try {
            // Check if the place exists
            $place = Place::where('id', $place_id)->first();
            // dd($place);
            if (!$place) {
                return response()->json(['error' => 'Place not found'], 404);
            }

            // Validate the request data
            $validator = Validator::make($request->all(), [
                // 'category_id' => 'required',
                'place_name' => 'required',
                'place_location' => 'required',
                'place_status' => 'required',
                'place_details' => 'required',
                // 'place_preview_video' => 'required',
                // 'place_link' => 'required',
                'amount' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            // Update the place record with the validated data
            $place->update($validator->validated());

            return response()->json([
                'message' => 'Place updated successfully!',
                'place' => $place
            ], 201);
        } catch (\Exception $e) {
            // Log the error message
            Log::error('Exception occurred' . $e->getMessage());

            // Return an appropriate error response
            if ($e instanceof ValidationException) {
                return response()->json(['error' => $e->errors()], 422);
            } else {
                return response()->json(['message' => 'An error occurred while updating the place.'], 501);
            }
        }
    }



    public function deletePlace($place_id)
    {
        try {
            $place = Place::where('id', $place_id)->first();
            if (!$place)
                return response()->json(['message' => 'Place not found try again!'], 404);

            $place->delete();
            return response()->json(['message' => 'Place deleted successfully!'], 201);
        } catch (\Exception $e) {
            Log::error('Exception occurred' . $e->getMessage());

            return response()->json(['message' => 'something happened while Deleting place'], 501);
        }
    }

    public function placeFeature($place_status)
    {
        try {
            $featuredPlaces = Place::where('place_status', $place_status)->get();
    
            if ($featuredPlaces->count() > 0) {
                $placeData = [];
    
                foreach ($featuredPlaces as $featuredPlace) {
                    // Retrieve images for the current place
                    $placeImages = Image::where('place_id', $featuredPlace->id)->pluck('image_path')->toArray();
    
                    // Retrieve free videos for the current place
                    $placeFeeVideos = FreeVideos::select(
                        'self_guided_short_version',
                        'short_eng_version_360_video',
                        'short_french_version_360_video',
                        'short_kiny_version_360_video'
                    )->where('place_id', $featuredPlace->id)->first();
    
                    // Check if free videos are found
                    $placeFreeVideos = $placeFeeVideos ? $placeFeeVideos->toArray() : [];
    
                    // Retrieve paid videos for the current place
                    $placePaidVideos = PaidVideos::select(
                        'long_version_self_guided',
                        'long_eng_version_360_video',
                        'long_french_version_360_video',
                        'long_kiny_version_360_video'
                    )->where('place_id', $featuredPlace->id)->first();
    
                    // Check if paid videos are found
                    $placePaidVideos = $placePaidVideos ? $placePaidVideos->toArray() : [];
    
                    // Assign images and videos to the current place object
                    $featuredPlace->images = $placeImages;
                    $featuredPlace->free_videos = $placeFreeVideos;
                    $featuredPlace->paid_videos = $placePaidVideos;
    
                    // Add the current place to the placeData array
                    $placeData[] = $featuredPlace;
                }
            } else {
                return response()->json(['message' => 'No featured places found'], 201);
            }
    
            return response()->json(['places' => $placeData], 201);
        } catch (\Exception $e) {
            Log::error('Error occurred: ' . $e->getMessage());
            return response()->json(['message' => 'Something happened while trying to get featured places'], 500);
        }
    }
    

    // public function placeFeature($place_status)
    // {
    //     try {
    //         $featuredPlaces = Place::where('place_status', $place_status)->get();

    //         if ($featuredPlaces->count()) {
    //             $placeData = [];

    //             foreach ($featuredPlaces as $featuredPlace) {
    //                 $placeImages = Image::where('place_id', $featuredPlace->id)->pluck('image_path')->toArray();
    //                 $placeVideos = Videos::where('place_id', $featuredPlace->id)->pluck( 
    //                 'self_guided_short_version',
    //                 'short_eng_version_360_video',
    //                 'short_french_version_360_video',
    //                 'short_kiny_version_360_video',

    //                 'long_version_self_guided',
    //                 'long_eng_version_360_video',
    //                 'long_french_version_360_video',
    //                 'long_kiny_version_360_video')->toArray();
    //                 $featuredPlace->images = $placeImages;
    //                 $featuredPlace->videos = $placeVideos;
    //                 //dd($featuredPlace);
    //                 $placeData[] = $featuredPlace;
    //             }
    //         } else {
    //             return response()->json(['message' => 'No featured places found'], 201);
    //         }

    //         return response()->json(['places' => $placeData], 201);
    //     } catch (\Exception $e) {
    //         Log::error('error occurred: ' . $e->getMessage());
    //         return response()->json(['message' => 'something happened while trying to get featured places'], 500);
    //     }
    // }
}

<?php

namespace App\Http\Controllers\RCHAcontroller;

use App\Models\Image;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class placeController extends Controller
{
    public function storePlace(Request $request)
    {
        $validator = Validator::make($request->All(), [
            'place_name' => 'required',
            'place_location' => 'required',
            'place_status' => 'required',
            'place_details' => 'required',
            'category_id'=> 'required',
            'place_preview_video' => 'required',
            'place_link' => 'required',
            
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
           //else create a place
           $place = Place::create($validator->validated());
        return response()->json(
            [
                'message' => 'Place is recorded successful!',
                'place' => $place
            ],
            201
        );
    }
    public function getPlaces()
{
    $place = Place::all();
    if($place)
    return response()->json($place, 200);
    return response()->json(['message' => 'Places are not found!'], 400);
}
public function getPlaceById(Request $request,$place_id)
{
    $place = Place::find($place_id);
//dd($place);
    if ($place === null) {
        return response()->json(['message' => 'Place not found try again!'], 400);
      }
      return response()->json($place, 200);
}
public function updatePlace(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'place_name' => 'sometimes|required',
        'place_location' => 'sometimes|required',
        'place_status' => 'sometimes|required',
        'place_details' => 'sometimes|required',
        'category_id' => 'sometimes|required',
        'place_preview_video' => 'sometimes|required', 
        'place_link' => 'sometimes|required'
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }

    $place = Place::find($id);
    $place->update($validator->validated());

    return response()->json([
        'message' => 'Place updated successfully!',
        'place' => $place
    ], 200);
}
public function deletePlace($id)
{
    $place = Place::find($id);
    if(!$place)
    return response()->json(['message' => 'Place not found try again!'], 400);
    
    $place->delete();
    return response()->json(['message' => 'Place deleted successfully!'], 200);
}
//this function will only select place which has featured as place_status
// public function placeFeature($place_status)
// {
//     try {
//         // Get the featured places.
//         $featuredPlaces = Place::where('place_status', $place_status)->get();

//         // If there are any featured places, get their images.
//         if ($featuredPlaces->count()) {
//             $placeImages = [];

//             foreach ($featuredPlaces as $featuredPlace) {
//                 $placeImages[$featuredPlace->id] = Image::where('place_id', $featuredPlace->id)->get();
//             }
//         } else {
//             // Return an empty response if there are no featured places.
//             return response()->json([], 200);
//         }

//         // Return the featured places and their images.
//         return response()->json(['places' => $featuredPlaces, 'images' => $placeImages], 200);
//     } catch (\Exception $e) {
//         Log::error('error accoured: ' . $e->getMessage());
//         return response()->json(['message' => 'something happed while trying to get featured places'], 500);
//     }
// }
public function placeFeature($place_status)
{
    try {
        $featuredPlaces = Place::where('place_status', $place_status)->get();

        if ($featuredPlaces->count()) {
            $placeImages = [];

            foreach ($featuredPlaces as $featuredPlace) {
                $placeImages[$featuredPlace->id] = Image::where('place_id', $featuredPlace->id)->get();
            }
        } else {
            return response()->json([], 200);
        }

        return response()->json(['places' => $featuredPlaces, 'images' => $placeImages], 200);
    } catch (\Exception $e) {
        Log::error('error accoured: ' . $e->getMessage());
        return response()->json(['message' => 'something happed while trying to get featured places'], 500);
    }

}
}
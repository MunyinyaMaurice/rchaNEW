<?php

namespace App\Http\Controllers\RCHAcontroller;

use App\Models\Image;

use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class imagesController extends Controller
{
    public function createImage(Request $request)
    {
        try {
            if (!$request->hasFile('file')) { // Changed to check for 'file' key
                return response()->json(['upload_file_not_found'], 404);
            }

            $file = $request->file('file');
            $imageName = time() . '.' . $file->hashName();
            $image_path = public_path() . '/images';
            // $image_path = public_path() . '/images/' . $imageName;

            $saveImage = $file->move($image_path, $imageName);

            if ($saveImage) {
                $place_id = Place::findOrNew($request->place_id);
                // dd($place_id->id);
                $save = new Image();
                $save->place_id = $place_id->id;
                $save->image_path = '/images/' . $imageName; // Adjusted to store the correct file path
                $save->save();

                return response()->json(['success' => true, 'message' => 'Image is uploaded and saved in DB'], 200);
            }

            return response()->json(['Image not saved in DB'], 422);
        } catch (\Exception $e) {
            Log::error('Exception occurred' . $e->getMessage());
            return response()->json(['message' => 'something happend while saving image in DB']);
        }
    }



    public function getImageById($image_id)
    {
        try {
            // $request->image_path;
            // $request->place_id;
            $image = Image::find($image_id);
            //dd($place);
            if ($image === null) {
                return response()->json(['message' => 'Image not found try again!'], 404);
            }
            return response()->json($image, 201);
        } catch (\Exception $e) {
            Log::error('Exception occurred' . $e->getMessage());
            return response()->json(['message' => 'something happend while getImageById']);
        }
    }

    public function updateImage(Request $request, $image_id)
{
    try {
        $request->validate([
            'place_id' => 'required|exists:places,id',
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Adjusted the validation rule for the file
        ]);

        $image = Image::find($image_id);

        if (!$image) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $imageName = time() . '.' . $file->getClientOriginalExtension();
            $image_path = public_path() . '/images/updatedImages';
            $saveImage = $file->move($image_path, $imageName);

            if ($saveImage) {
                $place = Place::find($request->place_id);

                if (!$place) {
                    return response()->json(['message' => 'Place not found'], 404);
                }

                $image->place_id = $place->id;
                $image->image_path = '/images/updatedImages/' . $imageName;
                $image->save();

                return response()->json([
                    'message' => 'Image updated successfully',
                    'image' => $image
                ], 201);
            }
            return response()->json(['message' => 'Failed to save image'], 422);
        }

        return response()->json(['message' => 'File not found in the request'], 404);
    } catch (\Exception $e) {
        Log::error('Exception occurred: ' . $e->getMessage());
        return response()->json(['message' => 'Something happened while updating the image'], 500);
    }
}


    public function deleteImage($image_id)
    {
        try {
            $image = Image::find($image_id);
            if($image){
            $image->delete();

            return response()->json([
                'message' => 'Image deleted successfully',
            ], 200);
        }
        return response()->json(['message' => 'image image not found']);
        } catch (\Exception $e) {
            Log::error('Exception occurred' . $e->getMessage());
            return response()->json(['message' => 'something happend while delete image']);
        }
    }
    public function getAllImages()
    {
        try {
            $images = Image::all();
            if ($images) {
                return response()->json(['images :' => $images], 201);
            }
            return response()->json(['no image found' => $images], 404);
        } catch (\Exception $e) {
            Log::error('error occured:' . $e->getMessage());
            return response()->json(['message' => 'error occured while getting all images']);
        }
    }
    public function getImageForPlace($place_id)
    {
        try {
            $place = Place::find($place_id);

            if (!$place) {
                return response()->json(['message' => 'Place not found'], 404);
            }

            $images = Image::where('place_id', $place_id)->get();

            if ($images->isEmpty()) {
                return response()->json(['message' => 'Images not found for the specified place'], 404);
            }

            $imageData = $images->map(function ($image) {
                return [
                    'image_id' => $image->id,
                    'image_path' => $image->image_path,

                ];
            });

            return response()->json([
                'place' => [
                    'place_name' => $place->place_name,
                ],
                'images' => $imageData,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error occurred: ' . $e->getMessage());
            return response()->json(['message' => 'Error occurred while getting images for the place by ID'], 500);
        }
    }
}

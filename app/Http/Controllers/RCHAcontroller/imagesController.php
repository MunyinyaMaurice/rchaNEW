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

    public function updateImage(Request $request, $id)
    {
        try {
            $request->validate([
                'place_id' => 'required|exists:places,id', // Corrected the validation rule
                'image_path' => 'required|string',
            ]);
            //dd($request);
            $image = Image::findOrFail($id);
            // dd($image);

            if (!$request->hasFile('file')) { // Changed to check for 'file' key
                return response()->json(['upload_file_not_found'], 404);
            }

            $file = $request->file('file');
            $imageName = time() . '.' . $file->hashName();
            $image_path = public_path() . '/images/updatedImages';
            $saveImage = $file->move($image_path, $imageName);

            if ($saveImage) {
                $place_id = Place::findOrNew($request->place_id);
                // dd($place_id->id);
                $image->place_id = $place_id->id;
                $image->image_path = '/images/updatedImages/' . $imageName;
                $image->save();

                return response()->json([
                    'message' => 'Image updated successfully',
                    'image' => $image
                ], 201);
            }
            return response()->json(['Image not updated in DB'], 422);
        } catch (\Exception $e) {
            Log::error('Exception occurred' . $e->getMessage());
            return response()->json(['message' => 'something happend while updating image']);
        }
    }

    public function deleteImage($id)
    {
        try {
            $image = Image::findOrFail($id);
            $image->delete();

            return response()->json([
                'message' => 'Image deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception occurred' . $e->getMessage());
            return response()->json(['message' => 'something happend while delete image']);
        }
    }
}

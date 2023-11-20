<?php

namespace App\Http\Controllers\RCHAcontroller;

use App\Models\Place;
use App\Models\Payment;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class feedbackController extends Controller
{
 

public function feedback(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'comment' => 'required',
            'rate' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        // Create a new feedback record.
        $feedback = Feedback::create($validator->validated());
     
//dd($result);
        // Return a success response.
        if ($feedback) {
            return response()->json([
                'message' => 'Feedback sent successfully.'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Error in sending feedback.'
            ], 500);
        }
       
    } catch (\Exception $e) {
        // Log the exception
        Log::error($e->getMessage());
        // Return an error response
        return response()->json([
            'message' => 'An error occurred while sending  feed-back.',
        ], 500);
    }
}

public function getAllFeedback(Request $request) {
    try {
        $perPage = $request->input('per_page', 20); // Default to 20 items per page

        $feedback = Feedback::paginate($perPage);

        if($feedback->count() > 0) {
            return $feedback;
        }

        return response()->json(['message'=>'no records found'], 404);
    } catch(\Exception $e) {
        Log::error($e->getMessage());
        return response(['message'=> 'An error occurred while fetching AllFeedback.'], 500);
    }
}

public function deleteFeedback($feedback_id){

   try{
    $feedback=Feedback::find($feedback_id);
    if($feedback->delete())
        {
            return response()->json(['message' =>'Feed-back is deleted!'],201);
        }
        return response()->json(['message'=>'no records found'],404);
    }catch(\Exception $e){
        Log::error($e->getMessage());
        return response(['message'=> 'An error occurred while fetching AllFeedback.'],500);

    }
    
}
}

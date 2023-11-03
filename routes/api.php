<?php


//use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Mail\sendFreeToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
// use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\userAuthController;
use App\Http\Controllers\DashboardController;
// use App\Http\Controllers\Auth\VerifyEmailController;
// use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\RCHAcontroller\placeController;
// use Illuminate\Foundation\Auth\EmailVerificationRequest;
// use App\Http\Controllers\RCHAcontroller\GoogleController;
use App\Http\Controllers\RCHAcontroller\imagesController;
use App\Http\Controllers\paymentGatways\flutterController;
// use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\RCHAcontroller\paymentController;
use App\Http\Controllers\RCHAcontroller\CategoryController;
use App\Http\Controllers\RCHAcontroller\feedbackController;
use App\Http\Controllers\RCHAcontroller\paymentInfoExportController;
// use App\Http\Controllers\Auth\EmailVerificationNotificationController;

Auth::routes([
    'verify'=>true
]);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    // Add other authenticated routes here...
});

Route::group(['middleware'=>'api','prefix'=>'auth'],function($router){

    Route::group(['middleware' => ['verified']], function() {
    Route::put('update-user', [userAuthController::class, 'updateUser']);

    Route::get('/profile',[userAuthController::class,'profile'])->name('profile');
    });

    Route::post('/login',[userAuthController::class,'login'])->name('login');
    Route::post('/register',[userAuthController::class,'register'])->name('register');
    Route::post('/logout',[userAuthController::class,'logout'])->name('logout'); 
   
    Route::post('sendPasswordResetLink', 'App\Http\Controllers\PasswordResetRequestController@sendEmail');
   // Route::post('resetPassword', 'App\Http\Controllers\ChangePasswordController@passwordResetProcess');
  

 //==============================================================================               

/** ROUTE ONLY FOR ADMIN  */

//================================================================================

 Route::group(['middleware' => 'admin'], function () {
               
/*  ROUTE FOR IMAGE CONTROLLER API*/
// Route::post('/upload-image',[imagesController::class,'createImage']);
Route::post('multiple-image-upload', [imagesController::class, 'createImage']);
Route::PUT('/updateImage/{id}', [imagesController::class, 'updateImage']);
Route::delete('/deleteImage/{id}', [imagesController::class, 'deleteImage']);
Route::get('/getImageById/{id}', [imagesController::class, 'getImageById']);


/*  ROUTE FOR PLACE CONTROLLER API*///
Route::post('/storeNewPlace',[placeController::class,'storePlace'])->name('storeNewPlace');
Route::get('/places', [placeController::class, 'getPlaces']);
Route::get('/place/{id}', [placeController::class, 'getPlaceById']);
Route::put('/updatePlace/{id}', [placeController::class, 'updatePlace']);
Route::delete('/deletePlace/{id}', [placeController::class, 'deletePlace']);

/** ROUTE FOR GETFEATURED PLACES*/
Route::get('/placeFeature/{place_status}',[placeController::class,'placeFeature']);

/** ROUTE FOR PAYMENT INFO CONTROLLER */
Route::post('/savePaymentinfo',[paymentController::class,'payment']);

Route::get('/processPaidLinks/{id}',[paymentController::class,'processPaidLink']);

/**     GET LIST OF ALL USERS */

Route::get('/getAllUser',[userAuthController::class,'getAllUser']);
Route::get('/calculateTotalAmountPaid',[paymentController::class,'calculateTotalAmountPaid']);
/** ROUTE FOR CATEGORY */
// Route::prefix('categories')->group(function () {
    Route::get('/listCategories',[CategoryController::class,'listCategories']);
    Route::get('/categories/{id}',[CategoryController::class,'getCategoryById']);
    Route::post('/categories',[CategoryController::class,'store']);
    Route::put('/categories/{id}',[CategoryController::class,'update']);
    Route::delete('/categories/{id}',[CategoryController::class,'delete']);
// });

});

//=====================================  ADIMIN ZONE  ENDs ================================================================

/** ROUTE TO DISPLAY USER HISTORICAL */
Route::get('/getPaymentInfo',[paymentController::class,'getPaymentInfo']);
Route::get('/showPaymentInfos', [paymentController::class,'showPaymentInfo']);

/** ROUTE FOR FLUTTERWAVE PAYMENT CONTROLLER */
// The route that the button calls to initialize payment
Route::post('/pay', [flutterController::class, 'initialize'])->name('pay');

// The callback url after a payment
Route::get('/rave/callback', [flutterController::class, 'callback'])->name('callback');


/**ROUTE FOR EXPORTING FILE IN EXCEL */
Route::get('/export-payment-info', [paymentInfoExportController::class, 'exportPaymentInfo']);

/** Generate paid and free Token and validate  */

Route::post('/generatePaidLink',[paymentController::class,'generatePaidLink']);
Route::get('/videoView/{paidToken}', [paymentController::class, 'validatePaidToken']);

/** GETTING FEEDBACK FROM USER */
Route::post('/feedback',[feedbackController::class,'feedback']);
Route::get('/getFeedback/{place_id}',[feedbackController::class,'getFeedback']);
Route::get('/getAllFeedback',[feedbackController::class,'getAllFeedback']);



/**ROUTE TO SEND PAID TOKEN EMAIL TO PAID USER */
Route::post('/sendVideoLinkView', function (Request $request) {
    try {
        $user = Auth::user();
        $place_id = $request->input('place_id');

        // Call the generatePaidLink method with a request object
        $paidLinkResponse = app('App\Http\Controllers\RCHAcontroller\paymentController')->generatePaidLink($request);

        // Get the JSON data from the response
        $data = $paidLinkResponse->getData();

        if (isset($data->paidToken)) {
                    Mail::to($user->email)
                ->send(new \App\Mail\sendVideoLink($user, $data->paidToken));

            return 'Email sent successfully!';
        }

        return 'Error generating paid link';

    } catch(\Exception $e) {
        Log::error('Exception occurred: ' . $e->getMessage());
        return response()->json([
            'message' => 'An error occurred while sending the paid token.',
        ], 500);
    }
});

/** SENDING FREE TOKEN TO CUSTOM EMAIL */

Route::post('/sendFreeToken', function (Request $request) {
    $paidToken = $request->input('paidToken');
    $recipientEmail = $request->input('email'); 
    try {
    Mail::to($recipientEmail)
        ->send(new sendFreeToken($paidToken));
    return 'Email sent successfully!';
    } catch(\Exception $e) {
        Log::error('Exception occurred: ' . $e->getMessage());
        return response()->json(['message'=>'An error occurred while sending the free token.'],500);
    }

});


Route::get('/home',function(){
    return view('home');
});

// Facebook Login URL
// Route::prefix('facebook')->name('facebook.')->group( function(){
//     Route::get('auth', [FaceBookController::class, 'loginUsingFacebook'])->name('login');
//     Route::get('callback', [FaceBookController::class, 'callbackFromFacebook'])->name('callback');
// });




});


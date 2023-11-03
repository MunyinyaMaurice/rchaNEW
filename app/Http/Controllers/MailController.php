<?php

namespace App\Http\Controllers;

 use App\Mail\SendMail; 
use Exception;
use App\Mail\MailNotify;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\Smtp\SmtpTransport;
use Swift_SmtpTransport;
use Swift_Mailer;
class MailController extends Controller
{
    // public function sendEmail(Request $request) { 
    //     $first_name = 'Thank you for your order'; 
    //     $last_name = [ 
    //     'first_name' => $request->get('first_name'), 
        
    //     'email' => $request->get('email') 
    //     ]; 
        
    //        $sendmail = Mail::to($last_name['email'])
    //        ->send(new SendMail($first_name, $last_name));
    //        if (empty($sendmail)) { 
    //          return response()->json(['message'
    //          => 'Mail Sent Sucssfully'], 200); 
    //          }else{ 
    //              return response()->json(['message' => 'Mail Sent fail'], 400); 
    //             }
    //          } 
//                  public function index()
//                  {
//                     $data = [
//                         'subject'=>'mail subject',
//                         'body'=>'hello it works'
//                     ];
//                     try{
// Mail::to('munyinya13@gmail.com')->send(new MailNotify($data));
// return response()->json(['great work!! now check your inbox']);
//                     }catch(Exception $e ){
//                         dd($e);
//                         return response()->json(['some went wrong']);
//                     }
                // }
                    // public function sendEmail() {
                    //     $username = config('mail.username');
                    //     $password = config('mail.password');
                    
                    //     Mail::send([], [], function($message) {
                    //         $message->to('munyinya13@gmail.com')
                    //             ->subject('Subject')
                    //             ->setBody('Body');
                    //     });
                    // }
                    
}

                 


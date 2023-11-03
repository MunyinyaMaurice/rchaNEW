<?php

// namespace App\Mail;

// use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Mail\Mailable;
// use Illuminate\Queue\SerializesModels;

// class SendMail extends Mailable
// {
//     use Queueable, SerializesModels;

//     public $first_name; 
//     public $last_name;

//     /**
//      * Create a new message instance.
//      *
//      * @return void
//      */
//     public function __construct($first_name, $last_name)
//     {
//         $this->first_name = $first_name; 
// 		$this->last_name= $last_name;
//     }

//     /**
//      * Build the message.
//      *
//      * @return $this
//      */
//     public function build()
//     {  // customer_mail is the name of template
//         return $this->subject($this->first_name) ->view('last_name');
//     }
// }

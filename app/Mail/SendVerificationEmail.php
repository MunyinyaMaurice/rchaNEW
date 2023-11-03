<?php
namespace App\Mail;

use App\Models\User;

use App\Http\Controllers\userAuthController;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendVerificationEmail extends Mailable
{
    use Queueable, SerializesModels;
    
        /**
         * Create a new message instance.
         * @var User
         */
    public $user;
   
        private $verificationToken;
    
        public function __construct(User $user)
        {
            $this->user = $user;
          
        }
    
        public function build()
        {
            return $this->subject('Verify your email address')
                ->markdown('emails.verify', [
                    'name'=> $this->user->last_name,
                    'email' => $this->user->email,
                    'id'=> $this->user->id,
                ]);
        }
        /**
         * Get the message envelope.
         */
        public function envelope(): Envelope
        {
            return new Envelope(
                subject: 'Mail Notify',
            );
        }
}

<?php


namespace App\Mail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class sendVideoLink extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     * @var User
     */
    private $user;
    // private $paidLink;
    private $paidToken;

    public function __construct(User $user,$paidToken)
    {
        $this->user =$user;
        $this->paidToken = $paidToken;
    }
    public function build()
    {
        
       return $this
       ->subject('Welcome to RCHA site')
       ->markdown('sendVideoLink.sendVideoLinkView',
    ['name'=> $this->user->last_name,
    'paidToken' => $this->paidToken,]);
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

    /**
     * Get the message content definition.
     */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'sendVideoLink.sendVideoLinkView',
    //     );
       
    // }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}


// namespace App\Mail;

// use App\Models\User;
// use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Mail\Mailable;
// use Illuminate\Mail\Mailables\Content;
// use Illuminate\Mail\Mailables\Envelope;
// use Illuminate\Queue\SerializesModels;

// class sendVideoLink extends Mailable
// {
//     use Queueable, SerializesModels;

//     /**
//      * Create a new message instance.
//      * @var User
//      */
//     private $user;
//     private $place_name;
//     private $place_id;
//     private $place_location;
//     private $amount;
//     private $paidToken;


//     public function __construct(User $user, $place_name, $place_id, $place_location, $amount, $paidToken)
//     {
//         $this->user = $user;
//         $this->place_name = $place_name;
//         $this->place_id = $place_id;
//         $this->place_location = $place_location;
//         $this->amount = $amount;
//         $this->paidToken = $paidToken;
//     }
//     public function build()
//     {

//         return $this
//             ->subject('Welcome to RCHA site')
//             ->markdown(
//                 'sendVideoLink.sendVideoLinkView',
//                 [
//                     'name' => $this->user->last_name,
//                     'paidToken' => $this->paidToken,
//                     'place_name'=> $this->placeName,
//                 ]
//             );
//     }
//     /**
//      * Get the message envelope.
//      */
//     public function envelope(): Envelope
//     {
//         return new Envelope(
//             subject: 'Mail Notify',
//         );
//     }

//     /**
//      * Get the message content definition.
//      */
//     // public function content(): Content
//     // {
//     //     return new Content(
//     //         view: 'sendVideoLink.sendVideoLinkView',
//     //     );

//     // }

//     /**
//      * Get the attachments for the message.
//      *
//      * @return array<int, \Illuminate\Mail\Mailables\Attachment>
//      */
//     public function attachments(): array
//     {
//         return [];
//     }
// }

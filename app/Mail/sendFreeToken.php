<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class sendFreeToken extends Mailable
{
    use Queueable, SerializesModels;

    private $FreeToken;

    public function __construct($FreeToken)
    {
        $this->FreeToken = $FreeToken;
    }

    public function build()
    {
        return $this
            ->subject('Welcome to RCHA site')
            ->markdown('sendVideoLink.sendFreeToken', [
                'FreeToken' => $this->FreeToken,
            ]);
    }
}

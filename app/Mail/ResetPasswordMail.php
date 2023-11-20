<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {  
        $url = env('APP_URL').'/response-reset?token='. $this->token;
        
        return $this->markdown('Email.passwordReset')->with(['url' => $url]);
    }
}

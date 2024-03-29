<?php

namespace App\Mail\Api;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisterToken extends Mailable {

    use Queueable,
        SerializesModels;

    public $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token) {
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->from('cgtdharm@gmail.com')
                        ->with([
                            'token' => $this->token,
                        ])
                        ->markdown('emails.api.register_token')
                        ->subject("Welcome to FLAIR");
    }

}

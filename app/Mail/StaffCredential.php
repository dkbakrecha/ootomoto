<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class StaffCredential extends Mailable {

    use Queueable,
        SerializesModels;

    public $userData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($userData) {
        $this->userData = $userData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->from('cgtdharm@gmail.com')
                        ->with([
                            'userData' => $this->userData,
                        ])
                        ->markdown('emails.staff_credentail');
    }

}
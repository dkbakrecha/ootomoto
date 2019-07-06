<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BookingReject extends Mailable
{
    use Queueable, SerializesModels;

    public $userData;
    public $bookingData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($userData, $bookingData) {
        $this->userData = $userData;
        $this->bookingData = $bookingData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('cgtdharm@gmail.com')
                        ->with([
                            'userData' => $this->userData,
                            'bookingData' => $this->bookingData,
                        ])
                        ->markdown('emails.booking_rejected');
    }
}

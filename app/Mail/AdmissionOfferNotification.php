<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdmissionOfferNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $name, $programme;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $programme)
    {
        $this->name = $name;
        $this->programme = $programme;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.admission_notification');
    }
}

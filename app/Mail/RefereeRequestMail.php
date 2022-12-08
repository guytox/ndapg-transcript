<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RefereeRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $link, $user, $name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $link, $name)
    {
        $this->user = $user;
        $this->link = $link;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.referee_request');
    }
}

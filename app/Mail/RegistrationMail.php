<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $dataUser;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($dataUser)
    {
        $this->dataUser = $dataUser;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject('[ICFMS '.date('Y').'] Signing up completed !');
        return $this->view('mail.registration');
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AbstractNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $dataAbstract;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( $dataAbstract)
    {
        $this->dataAbstract = $dataAbstract;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $details = $this->dataAbstract['user']->userDetails;
        $this->subject('Abstract Submitted');
        return $this->view('mail.abstract-notification')->with([
            'name'  => implode(' ', [$details->firstname, $details->midlename, $details->lastname]),
            'affiliation' => $details->affiliation,
            'country' => $details->country,
            'title' => $details->title,
        ]);
    }
}

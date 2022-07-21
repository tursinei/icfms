<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AnnouncementMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject($this->mailData['title']);
        if(isset($this->mailData['attachment'])){
            $this->attach($this->mailData['attachment'], [
                'as' => $this->mailData['file_name'],
                'mime' => $this->mailData['file_mime'],
            ]);
        }
        return $this->html($this->mailData['isi_email']);
    }
}

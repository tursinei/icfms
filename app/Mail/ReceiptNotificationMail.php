<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReceiptNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $userDetail, $pathReceipt;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($userDetail, $pathReceipt)
    {
        $this->userDetail = $userDetail;
        $this->pathReceipt = $pathReceipt;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject('Payment Receipt - IcAUMS ' . date('Y'));
        $details = $this->userDetail;
        return $this->view('mail.receipt-payment')->with([
            'name'  => implode(' ', [$details->firstname, $details->midlename, $details->lastname]),
            'affiliation' => $details->affiliation,
            'country' => $details->country,
            'title' => $details->title,
        ])->attach($this->pathReceipt);
    }
}

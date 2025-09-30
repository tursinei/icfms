<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReceiptNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $user, $invoice;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $invoice)
    {
        $this->user = $user;
        $this->invoice = $invoice;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject('Payment Receipt - ICFMS ' . date('Y'));
        $details = $this->user->userDetails;
        return $this->view('mail.receipt-payment')->with([
            'name'  => implode(' ', [$details->firstname, $details->midlename, $details->lastname]),
            'affiliation' => $details->affiliation,
            'country' => $details->country,
            'title' => $details->title,
            'jenis' => $this->invoice->jenis,
        ])->attach($this->invoice->path);
    }
}

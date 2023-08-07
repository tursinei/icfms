<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $userDetail, $invoice;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($userDetail, $invoice)
    {
        $this->userDetail = $userDetail;
        $this->invoice = $invoice;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject('Payment Invoice - IcAUMS ' . date('Y'));
        $details = $this->userDetail;
        return $this->view('mail.invoice')->with([
            'name'  => implode(' ', [$details->firstname, $details->midlename, $details->lastname]),
            'affiliation' => $details->affiliation,
            'country' => $details->country,
            'title' => $details->title,
            'jenis' => $this->invoice->jenis,
        ])->attach($this->invoice->path);
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Mailer extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        //
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->details['type'] == 'confirmemail')
            return $this->subject($this->details['subject'])
                ->view('email.confirm')
                ->from($this->details['from'], $this->details['from']);
        if($this->details['type'] == 'changepassword')
            return $this->subject($this->details['subject'])
                ->view('email.changepassword')
                ->from($this->details['from'], $this->details['from']);
        if($this->details['type'] == 'depositsuccess')
            return $this->subject($this->details['subject'])
                ->view('email.depositsuccess')
                ->from($this->details['from'], $this->details['from']);
        if($this->details['type'] == 'withdrawsuccess')
            return $this->subject($this->details['subject'])
                ->view('email.withdrawsuccess')
                ->from($this->details['from'], $this->details['from']);
        if($this->details['type'] == 'confirmwithdraw')
            return $this->subject($this->details['subject'])
                ->view('email.confirmwithdraw')
                ->from($this->details['from'], $this->details['from']);
        if($this->details['type'] == 'depositfiatconfirm')
            return $this->subject($this->details['subject'])
                ->view('email.depositfiatconfirm')
                ->from($this->details['from'], $this->details['from']);
        if($this->details['type'] == 'depositsuccesstoadmin')
            return $this->subject($this->details['subject'])
                ->view('email.depositsuccesstoadmin')
                ->from($this->details['from'], $this->details['from']);
    }
}

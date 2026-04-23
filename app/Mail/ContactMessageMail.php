<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessageMail extends Mailable
{
    use SerializesModels;

    public $name;
    public $email;
    public $subjectText;
    public $messageText;

    public function __construct(array $data)
    {
        $this->name        = $data['name'];
        $this->email       = $data['email'];
        $this->subjectText = $data['subject'];
        $this->messageText = $data['message'];
    }

    public function build()
    {
        return $this
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->replyTo($this->email, $this->name)
            ->subject('[Contact] ' . $this->subjectText)
            ->view('emails.contact_message'); // 👈 EXACT view name
    }
}

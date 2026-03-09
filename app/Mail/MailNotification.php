<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $body_message;
    public $subject;
    public $action;

    /**
     * Create a new bodyMessage instance.
     *
     * @return void
     */
    public function __construct($payload)
    {
        $this->subject = $payload['subject'];
        $this->body_message = $payload['body'];
        $this->action = $payload['action'] ?? null;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject($this->subject)
            ->markdown('emails.mailNotification');
    }
}

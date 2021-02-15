<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    private $token;

    /**
     * Create a new instance.
     *
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): ConfirmEmail
    {
        return $this->view('emails.confirm_email')
            ->with([
                'url' => url('email/confirm/' . $this->token),
            ]);
    }
}

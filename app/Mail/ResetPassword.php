<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    private $token;

    /**
     * @var int
     */
    private $expiration;

    /**
     * Create a new instance.
     *
     * @param string $token
     * @param int $expiration
     */
    public function __construct(string $token, int $expiration)
    {
        $this->token = $token;
        $this->expiration = $expiration;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): ResetPassword
    {
        return $this->subject('Reset password')
            ->view('emails.reset_password')
            ->with([
                'app' => config('app.name'),
                'expiration' => $this->expiration,
                'url' => ui_url('password/reset/' . $this->token),
            ]);
    }
}

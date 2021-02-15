<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserActivation extends Mailable
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
    public function build(): UserActivation
    {
        return $this->view('emails.user_activation')
            ->with([
                'token' => $this->token,
                'url' => config('app.url'),
            ]);
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InviteUser extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    private $token;

    /**
     * @var bool
     */
    private $isNewUser;

    /**
     * @var string
     */
    private $inviter;

    /**
     * Create a new instance.
     *
     * @param string $token
     * @param string $inviter
     * @param bool $isForExistingUser
     */
    public function __construct(string $token, string $inviter, bool $isForExistingUser)
    {
        $this->token = $token;
        $this->isNewUser = !$isForExistingUser;
        $this->inviter = $inviter;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): InviteUser
    {
        $url = $this->isNewUser ?
            url('register?token='.$this->token) :
            url('users/invite/'.$this->token);

        return $this->subject('Invitation to use "'.config('app.name').'"')
            ->view('emails.invite_user')
            ->with([
                'url' => $url,
                'inviter' => $this->inviter,
            ]);
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeleteUser extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): DeleteUser
    {
        return $this->subject('Your account was deleted')
            ->view('emails.delete_user')
            ->with([
                'app' => config('app.name'),
            ]);
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendVerificationData extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $verification_code;
    protected $verification_link;
    protected $name;
    protected $email;

    public function __construct($code, $link, $email, $name)
    {
        $this->verification_code = $code;
        $this->verification_link = $link;
        $this->name = $name;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('auth.verification_email')
            ->from(env('MAIL_USERNAME'), env('APP_NAME'))
            ->subject('Confirmation Code')
            ->with(['verification_code' => $this->verification_code,
                'url' => $this->verification_link,
                'name' => $this->name,
                'email' => $this->email]);

    }
}
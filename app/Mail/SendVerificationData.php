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
    protected $context;
    protected $button_msg;
    public $subject;

    public function __construct($code, $link, $name, $context, $subject = 'Verify account', $button_msg = 'Verify email')
    {
        $this->verification_code = $code;
        $this->verification_link = $link;
        $this->name = $name;
        $this->context = $context;
        $this->button_msg = $button_msg;
        $this->subject = $subject;
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
            ->subject($this->subject)
            ->with(['verification_code' => $this->verification_code,
                'url' => $this->verification_link,
                'name' => $this->name,
                'context' => $this->context,
                'button_msg' => $this->button_msg
            ]);

    }
}
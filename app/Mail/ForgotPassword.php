<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;

class ForgotPassword extends Mailable
{
    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function build(): ForgotPassword
    {
        $passwordCode = $this->data['passwordCode'];
        return $this->view('emails.forgot_password_mail', [
            'passwordCode' => $passwordCode
        ])
            ->subject('Salasanan palauttaminen');
    }
}

<?php

namespace App\Managers;

use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPassword;
use App\Mail\Report;

class EmailManager
{
    public function sendForgotPasswordEmail(string $recipientEmail, int $code): string
    {
        $recipientEmail = $recipientEmail;
        $emailData = [
            'passwordCode' => $code
        ];

        Mail::to($recipientEmail)->send(new ForgotPassword($emailData));

        return 'Email sent successfully';
    }

    public function sendReportEmail(string $reported, string $reporter, string $content): string
    {
        $recipientEmail = 'support@ticketless.fi';
        $emailData = [
            'reported' => $reported,
            'reporter' => $reporter,
            'content' => $content
        ];

        Mail::to($recipientEmail)->send(new Report($emailData));

        return 'Email sent successfully';
    }
}

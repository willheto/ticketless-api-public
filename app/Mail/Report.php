<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;

class Report extends Mailable
{
    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function build(): Report
    {
        $reported = $this->data['reported'];
        $reporter = $this->data['reporter'];
        $content = $this->data['content'];

        return $this->view('emails.report', [
            'reported' => $reported,
            'reporter' => $reporter,
            'content' => $content
        ])
            ->subject('Ilmianto');
    }
}

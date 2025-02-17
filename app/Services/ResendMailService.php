<?php

namespace App\Services;
use Resend\Resend;


class ResendMailService
{
    protected $resend;

    public function __construct()
    {
        $this->resend = new Resend(env('MAIL'));
    }

    public function sendMail($to, $subject, $htmlContent)
    {
        return $this->resend->emails->send([
            'from' => env('MAIL_FROM_ADDRESS'),
            'to' => $to,
            'subject' => $subject,
            'html' => $htmlContent,
        ]);
    }
}

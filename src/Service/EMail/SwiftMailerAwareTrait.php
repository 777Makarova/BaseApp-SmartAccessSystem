<?php

namespace App\Service\EMail;

use Swift_Mailer;

trait SwiftMailerAwareTrait
{
    /**
     * @var Swift_Mailer
     */
    protected $mailer;

    public function setSwiftMailer(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;

        return $this;
    }
}

<?php

namespace App\Service\Code;

use App\Entity\Code\Code;

class CodeSender extends Sender
{
    public const NAME = 'any';

    protected EmailSender $emailSender;
    protected SmsSender $smsSender;

    public function __construct(EmailSender $emailSender, SmsSender $smsSender)
    {
        $this->emailSender = $emailSender;
        $this->smsSender = $smsSender;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function sendCode(Code $code)
    {
        switch ($code->sentBy) {
            case Code::SENT_BY_EMAIL:
                $this->emailSender->sendCode($code);
                break;
            case Code::SENT_BY_SMS:
                $this->smsSender->sendCode($code);
                break;
        }
    }
}

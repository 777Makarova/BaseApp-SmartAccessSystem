<?php

namespace App\Service\Code;

use App\Entity\Code\Code;
use InvalidArgumentException;
use Symfony\Contracts\Translation\TranslatorInterface;

class SmsSender implements SenderInterface
{
    protected $smsService;

    public const NAME = 'sms';

    public function __construct(
        SendSmsService $smsService,
        private TranslatorInterface $translator)
    {
        $this->smsService = $smsService;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function sendCode(Code $code): void
    {
        $phone = $code->phone ?? $code->user->phone ?? null;
        if (empty($phone)) {
            throw new InvalidArgumentException($this->translator->trans('user.authorization.confirmation.errors.no_phone_from_code'));
        }

        $this->smsService->sendMessage($phone, $code->code, $code->user);
    }
}

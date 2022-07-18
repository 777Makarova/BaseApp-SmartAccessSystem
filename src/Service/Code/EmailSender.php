<?php

namespace App\Service\Code;

use App\Entity\Code\Code;
use App\Service\Code\Factories\MailFactory;
use InvalidArgumentException;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmailSender extends Sender
{
    protected $mailFactory;
    // TODO: make them translatable
    protected $subjectByCodeType = [
        Code::TYPE_ACTIVATE => 'Активация аккаунта',
        Code::TYPE_CONFIRM_EMAIL => 'Подтверждение почты',
        Code::TYPE_RESET_PASSWORD => 'Смена пароля',
    ];
    protected $templateByCodeType = [
        Code::TYPE_ACTIVATE => 'activate_mail.html.twig',
        Code::TYPE_CONFIRM_EMAIL => 'confirm_mail.html.twig',
        Code::TYPE_RESET_PASSWORD => 'reset_password.html.twig',
    ];

    public const NAME = 'email';

    public function __construct(
        MailFactory $mailFactory,
        private TranslatorInterface $translator)
    {
        $this->mailFactory = $mailFactory;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function sendCode(Code $code)
    {
        $email = $code->email ?? $code->user->getEmail() ?? null;
        if (empty($email)) {
            throw new InvalidArgumentException($this->translator->trans('user.authorization.confirmation.errors.no_email_from_code'));
        }

        $subject = $this->subjectByCodeType[$code->codeType] ?? 'Код подтверждения';
        $template = $this->templateByCodeType[$code->codeType] ?? 'confirm_code_default.html.twig';

        $this->mailFactory
            ->create()
            ->setSubject($subject)
            ->setContentType('text/html')
            ->setBodyFromTemplate('confirm_codes/'.$template, [
                'code' => $code,
                'user' => $code->user,
            ])
            ->setTo($code->user->getEmail())
            ->send()
        ;
    }
}

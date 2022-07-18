<?php

namespace App\Service\Code;

use App\Entity\Code\Code;
use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CodeService
{
    protected EntityManagerInterface $em;
    protected LoggerInterface $logger;
    protected CodeSender $codeSender;

    public int $smsCodeLifetime = 3600;
    public int $emailCodeLifetime = 86400;
    public int $lifetime = 86400;

    /**
     * @var array|SenderInterface[]
     */
    protected array $sendersByName = [];

    public function __construct(
        EntityManagerInterface $em,
        LoggerInterface $logger,
        CodeSender $codeSender,
        private TranslatorInterface $translator
    ) {
        $this->logger = $logger;
        $this->em = $em;
        $this->codeSender = $codeSender;
    }

    public function setSenders($senders)
    {
        foreach ($senders as $sender) {
            if (!$sender instanceof SenderInterface) {
                $type = is_object($sender) ? get_class($sender) : gettype($sender);
                throw new InvalidArgumentException(
                    sprintf('sender must implement %s, %s given', SenderInterface::class, $type)
                );
            }
            $this->sendersByName[$sender->getName()] = $sender;
        }
    }

    public function sendCode(Code $code)
    {
        $sender = $this->sendersByName[$code->sentBy] ?? null;
        if (empty($sender)) {
            throw new InvalidArgumentException($this->translator->trans('user.authorization.confirmation.errors.no_sender_code'));
        }

        return $sender->sendCode($code);
    }

    public function sendActivateCodeForUser(User $user, $type = Code::TYPE_ACTIVATE): ?Code
    {
        switch ($user->activationType) {
            case Code::SENT_BY_EMAIL:
                $code = $this->createCodeWithEmail($user, $user->email, $type);
                break;
            case Code::SENT_BY_SMS:
                $code = $this->createCodeWithPhone($user, $user->phone, $type);
                break;
            default:
                // do nothing
                return null;
        }

        $this->em->persist($code);
        $this->em->flush();

        $this->codeSender->sendCode($code);

        return $code;
    }

    public function createCode(User $user, $codeType, $sentBy): Code
    {
        $code = new Code($user, $codeType, $sentBy, $this->lifetime);
        /** @noinspection PhpUnhandledExceptionInspection */
        $code->code = $this->generateToken();

        return $code;
    }

    public function createCodeWithPhone(User $user, $phone, $codeType): Code
    {
        $code = new Code($user, $codeType, Code::SENT_BY_SMS, $this->smsCodeLifetime);
        $code->code = $this->generateTokenForSms();
        $code->phone = $phone;

        return $code;
    }

    public function createCodeWithEmail(User $user, $email, $codeType): Code
    {
        $code = new Code($user, $codeType, Code::SENT_BY_EMAIL, $this->emailCodeLifetime);
        /** @noinspection PhpUnhandledExceptionInspection */
        $code->code = $this->generateToken();
        $code->email = $email;

        return $code;
    }

    /**
     * I expected there's standard method&interface for that.
     *
     * @param int $bytes
     *
     * @return string
     * @throws Exception
     */
    public function generateToken(int $bytes = 32): string
    {
        return rtrim(strtr(base64_encode(random_bytes($bytes)), '+/', '-_'), '=');
    }

    public function generateTokenForSms(): int
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return random_int(1000, 9999);
    }

    /**
     * @param CodeSender $codeSender
     */
    public function setCodeSender(CodeSender $codeSender): void
    {
        $this->codeSender = $codeSender;
    }
}

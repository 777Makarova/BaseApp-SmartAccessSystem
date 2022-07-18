<?php

namespace App\Entity\Code;

use App\Entity\BaseEntity;
use App\Entity\User\User;
use DateInterval;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use InvalidArgumentException;

#[ORM\Entity]
#[ORM\Table(name: "activate_code")]
class Code extends BaseEntity
{
    public const TYPE_ACTIVATE = 'activate';
    public const TYPE_CONFIRM_EMAIL = 'confirm_email';
    public const TYPE_CONFIRM_PHONE = 'confirm_phone';
    public const TYPE_RESET_PASSWORD = 'reset_password';
    public const TYPE_TWO_FACTOR_AUTH = 'two_factor_auth';

    public const SENT_BY_EMAIL = 'email';
    public const SENT_BY_SMS = 'sms';

    public function __construct(User $user = null, $codeType = null, $sentBy = null, $lifetime = null)
    {
        parent::__construct();
        $this->user = $user;
        $this->codeType = $codeType;
        $this->sentBy = $sentBy;
        if (null !== $lifetime) {
            $this->setLifetime($lifetime);
        }
    }

    #[ORM\Column(nullable: false)]
    public string $code;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "code")]
    #[ORM\JoinColumn(nullable: false)]
    public ?User $user;

    #[ORM\Column(type: "string", nullable: false)]
    public string $codeType;

    #[ORM\Column(type: "string", nullable: true)]
    public ?string $sentBy = null;

    #[ORM\Column(type: "string", nullable: true)]
    public ?string $phone = null;

    #[ORM\Column(type: "string", nullable: true)]
    public ?string $email = null;

    #[ORM\Column(type: "integer", nullable: false)]
    public int $expiresAt;

    public function isExpired($time = null): bool
    {
        if (is_null($time)) {
            $time = time();
        } elseif ($time instanceof \DateTimeInterface) {
            $time = $time->getTimestamp();
        }

        return $time > $this->expiresAt;
    }

    /**
     *
     * @param DateInterval|int|string $time
     * @throws Exception
     */
    public function setLifetime(DateInterval|int|string $time)
    {
        if (is_int($time) || is_float($time)) {
            $this->setLifetimeFromSeconds($time);
        } elseif (is_string($time)) {
            $this->setLifetimeFromDateInterval(new DateInterval($time));
        } elseif ($time instanceof DateInterval) {
            $this->setLifetimeFromDateInterval($time);
        } else {
            throw new InvalidArgumentException();
        }
    }

    public function setLifetimeFromDateInterval(DateInterval $interval)
    {
        $this->expiresAt = (new DateTime())->add($interval)->getTimestamp();
    }

    public function setLifetimeFromSeconds(float $time)
    {
        $this->expiresAt = time() + $time;
    }
}

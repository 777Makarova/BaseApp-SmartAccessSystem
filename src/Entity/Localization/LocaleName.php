<?php

namespace App\Entity\Localization;

use App\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class LocaleName extends BaseEntity
{
    public const DEFAULT_LOCALE = 'ru';

    public const AVAILABLE_LOCALES = [
        'ru',
        'en',
        'ua',
        'de',
    ];

    #[ORM\Column(type: "array", nullable: true)]
    #[Groups(["GetLocaleName"])]
    public ?array $nameRu = null;

    #[ORM\Column(type: "array", nullable: true)]
    #[Groups(["GetLocaleName"])]
    public ?array $nameEn = null;

    #[ORM\Column(type: "array", nullable: true)]
    #[Groups(["GetLocaleName"])]
    public ?array $nameUa = null;

    #[ORM\Column(type: "array", nullable: true)]
    #[Groups(["GetLocaleName"])]
    public ?array $nameDe = null;
}

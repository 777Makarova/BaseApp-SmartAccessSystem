<?php


namespace App\Entity\Localization;


use App\Entity\Localization\LocaleName;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait LocalizationTrait
{
    #[ORM\OneToOne(targetEntity: LocaleName::class, cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    #[Groups(["GetLocaleName", "SetLocaleName"])]
    public ?LocaleName $localizeNameList = null;

    #[Groups(["GetLocaleName"])]
    public string|array|null $nameInCurrentLocale;

    public function setNameByLocale(string $locale = LocaleName::DEFAULT_LOCALE): self
    {
        $fieldName = $this->getPropertyName($locale);
        $value = $this->localizeNameList?->$fieldName;

        if (is_array($value) && count($value) == 1) {
            $this->nameInCurrentLocale = $value[array_key_first($value)];
            return $this;
        }

        $this->nameInCurrentLocale = $value;
        return $this;
    }

    public function getPropertyName(string $locale = LocaleName::DEFAULT_LOCALE): ?string
    {
        return 'name' . ucfirst($this->normalizeLocale($locale));
    }

    private function normalizeLocale(string $locale): string
    {
        if (!in_array(strtolower($locale), LocaleName::AVAILABLE_LOCALES, true)) {
            $locale = LocaleName::DEFAULT_LOCALE;
        }

        return strtolower($locale);
    }

    public function createNameList(array $fields): self
    {
        $localeNameList = new LocaleName();
        foreach ($fields as $field => $value) {
            if (property_exists(LocaleName::class, $field)) {
                if (is_string($value)) {
                    $value = [$value];
                }

                $localeNameList->$field = $value;
            }
        }

        $this->localizeNameList = $localeNameList;
        return $this;
    }
}

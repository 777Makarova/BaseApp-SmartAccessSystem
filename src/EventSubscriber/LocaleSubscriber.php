<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Localization\LocaleName;
use App\Entity\Localization\LocalizationTrait;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    #[ArrayShape([KernelEvents::VIEW => "array"])]
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['preSerialize', EventPriorities::PRE_SERIALIZE],
        ];
    }

    /**
     * @throws \Exception
     */
    public function preSerialize(ViewEvent $viewEvent)
    {
        $data = $viewEvent->getControllerResult();
        $data = $this->execute($data);

        if (empty($data)) {
            return;
        }

        $locale = $viewEvent->getRequest()->getLocale() ?? LocaleName::DEFAULT_LOCALE;

        if (is_array($data)) {
            foreach ($data as $localizableObject) {
                $localizableObject->setNameByLocale($locale);
            }
        } else {
            $data->setNameByLocale($locale);
        }
    }

    /**
     * @throws \Exception
     */
    private function execute($data): ?array
    {
        if ($data instanceof Paginator) {
            $array = iterator_to_array($data->getIterator());
            $object = @$array[0];
            if (is_null($object)) {
                return null;
            }

            if (in_array(LocalizationTrait::class, class_uses($object), true)) {
                return $array;
            }
        } elseif (is_array($data)) {
            $object = @$data[0];
            if (is_null($object)) {
                return null;
            }
            if (in_array(LocalizationTrait::class, class_uses($object), true)) {
                return $data;
            }
        } elseif (is_object($data)) {
            if (in_array(LocalizationTrait::class, class_uses($data), true)) {
                return $data;
            }
        }

        return null;
    }
}

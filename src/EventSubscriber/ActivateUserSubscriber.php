<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User\User;
use App\Service\Code\CodeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ActivateUserSubscriber implements EventSubscriberInterface
{
    private CodeService $codeService;
    private EntityManagerInterface $em;

    public const REQUEST_ATTRIBUTE_NAME = 'sendActivateFor';

    public function __construct(CodeService $activateUserService, EntityManagerInterface $em)
    {
        $this->codeService = $activateUserService;
        $this->em = $em;
    }

    public static function getSubscribedEvents():array
    {
        return [
            KernelEvents::VIEW => ['sendActivate', EventPriorities::POST_WRITE],
        ];
    }

    public function sendActivate(ViewEvent $event)
    {
        $sendActivateFor = $event->getRequest()->attributes->get(self::REQUEST_ATTRIBUTE_NAME);
        if ($sendActivateFor instanceof User) {
            $this->codeService->sendActivateCodeForUser($sendActivateFor);
        }
    }
}

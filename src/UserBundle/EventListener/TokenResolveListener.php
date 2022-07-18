<?php


namespace App\UserBundle\EventListener;


use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use League\Bundle\OAuth2ServerBundle\Event\TokenRequestResolveEvent;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TokenResolveListener
{

    public function __construct(private EntityManagerInterface $_em,private TokenStorageInterface $tokenStorage)
    {
    }

    public function onTokenResolve(TokenRequestResolveEvent $event):void
    {

        $token = $this->tokenStorage->getToken();
        $this->tokenStorage->setToken();

    }
}

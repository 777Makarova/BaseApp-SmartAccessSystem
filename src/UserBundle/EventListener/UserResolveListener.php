<?php


namespace App\UserBundle\EventListener;


use     App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use League\Bundle\OAuth2ServerBundle\Event\UserResolveEvent;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class UserResolveListener
{

    public function __construct(private EntityManagerInterface $_em, private UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * @throws OAuthServerException
     */
    public function onUserResolve(UserResolveEvent $event):void
    {

        /** @var User $user */
        $user = $this->_em->getRepository(User::class)->findOneBy(['username' => $event->getUsername()]);

        if($user === null){
            throw OAuthServerException::invalidCredentials();
        }

        if(!$this->passwordHasher->isPasswordValid($user, $event->getPassword())){
            throw OAuthServerException::invalidCredentials();
        }
        else{
            $event->setUser($user);
        }

    }
}

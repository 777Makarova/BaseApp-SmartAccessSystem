<?php


namespace App\Security\User;



use App\Entity\User\User;
use App\Service\User\UserManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    public function __construct(private UserManager $userManager){

    }
    /**
     * Метод loadUserByIdentifier() был представлен в Symfony 5.3.
     * В предыдущих версиях он назывался loadUserByUsername()
     *
     * Symfony вызывает этот метод, если вы используете функции вроде switch_user
     * или remember_me. Если вы не используете эти функции, вам не нужно реализовывать
     * этот метод.
     *
     * @throws UserNotFoundException if the user is not found
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        // Загрузить объект User из вашего источника данных или вызвать UserNotFoundException.
        // Аргумент $identifier - то значение, которое возвращается методом
        // getUserIdentifier() в вашем классе User.
        return $this->userManager->findUserByIdentifier($identifier);
    }

    public function loadUserByUsername(string $username): UserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    /**
     * Обновляет пользователя после повторной загрузки из сессии.
     *
     * Когда пользователь вошел в систему, в начале каждого запроса, объект
     * User загружается из сессии, а затем вызывается этот метод. Ваша задача
     * - убедиться, что данные пользователя все еще свежие, путем, к примеру,
     * повторного запроса свежих данных пользователя.
     *
     * Если ваш файерволл "stateless: true" (для чистого API), этот метод
     * не вызывается.
     *
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user):UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        // Вернуть объект User после того, как убедились, что его данные "свежие".
        // Или вызвать UserNotFoundException, если пользователь уже не существует.
        throw new \Exception('TODO: fill in refreshUser() inside '.__FILE__);
    }

    /**
     * Tells Symfony to use this provider for this User class.
     */
    public function supportsClass(string $class):bool
    {
        return User::class === $class || is_subclass_of($class, User::class);
    }

    /**
     * Upgrades the encoded password of a user, typically for using a better hash algorithm.
     * @param PasswordAuthenticatedUserInterface  $user
     * @param string $newEncodedPassword
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newEncodedPassword):void
    {
        // СДЕЛАТЬ: когда используются хешированные пароли, этот метод должен:
        // 1. сохранять новый пароль в хранилище пользователя
        // 2. обновлять объект $user с $user->setPassword($newHashedPassword);
    }

}

<?php


namespace App\Security;

use App\Entity\Account\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * @var class-string
     */
    private string $className = User::class;

    public function __construct(private ManagerRegistry $managerRegistry)
    {
    }

    public function loadUserByUsername(string $username)
    {
        $user = $this->findUser($username);
        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username %s does not exists', $username));
        }

        return $user;
    }

    private function findUser($username)
    {
        return $this->managerRegistry->getRepository($this->className)->findOneBy(
            [
                'username' => $username,
            ]
        );
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Expect %s got %s', $this->className, get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass(string $class)
    {
       return $this->className === $class;
    }
}
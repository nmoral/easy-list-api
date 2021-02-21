<?php

namespace App\Security;

use App\Entity\Account\User;
use App\Repository\UserRepository;
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

    public function __construct(
        private UserRepository $repository
    ) {
    }

    public function loadUserByUsername(string $username): User | UserInterface
    {
        $user = $this->findUser($username);
        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username %s does not exists', $username));
        }

        return $user;
    }

    private function findUser(string $username): ?User
    {
        return $this->repository->findOneBy(
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

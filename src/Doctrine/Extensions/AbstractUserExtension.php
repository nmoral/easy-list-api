<?php


namespace App\Doctrine\Extensions;

use App\Entity\Account\User;
use Symfony\Component\Security\Core\Security;

abstract class AbstractUserExtension
{
    public function __construct(
        private Security $security,
        private string $className
    )
    {
    }

    public function getSupportedClass(): string
    {
        return $this->className;
    }

    protected function getUser(): ?User
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return null;
        }

        return $user;
    }
}
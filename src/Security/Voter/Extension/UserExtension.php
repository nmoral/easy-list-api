<?php

namespace App\Security\Voter\Extension;

use App\Entity\Account\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserExtension extends AbstractExtension implements VoterExtensionInterface
{
    public function __construct()
    {
        parent::__construct(User::class);
    }

    protected function onRead(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        return $subject === $user;
    }

    protected function onUpdate(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        return $subject === $user;
    }

    protected function onCreate(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return true;
    }

    protected function onDelete(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return false;
    }
}

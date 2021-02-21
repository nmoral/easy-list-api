<?php

namespace App\Security\Voter\Extension;

use App\Entity\Account\User;
use App\Entity\TodoList\BulletPoint;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class BulletPointExtension extends AbstractExtension implements VoterExtensionInterface
{
    public function __construct()
    {
        parent::__construct(BulletPoint::class);
    }

    protected function onRead(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        return $subject->isWatcher($user);
    }

    protected function onUpdate(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        return $subject->isModifier($user);
    }

    protected function onCreate(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        return $subject->isModifier($user);
    }

    protected function onDelete(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        return $subject->isModifier($user);
    }
}

<?php

namespace App\Security\Voter\Extension;

use App\Entity\Account\User;
use App\Entity\TodoList\TodoList;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TodoListExtension extends AbstractExtension implements VoterExtensionInterface
{
    public function __construct()
    {
        parent::__construct(TodoList::class);
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
        return true;
    }

    protected function onDelete(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        return $subject->isOwner($user);
    }
}

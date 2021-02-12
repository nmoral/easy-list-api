<?php

namespace App\Security\Voter\Extension;

use App\Entity\Account\User;
use App\Entity\TodoList\ListManager;
use App\Entity\TodoList\TodoList;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use function foo\func;

class TodoListExtension extends AbstractExtension implements VoterExtensionInterface
{

    #[Pure] public function __construct()
    {
        parent::__construct(TodoList::class);
    }

    /**
     * @param $attribute
     * @param TodoList $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function onRead($attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        return $subject->isWatcher($user);
    }

    /**
     * @param $attribute
     * @param TodoList $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function onUpdate($attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        return $subject->isModifier($user);
    }

    protected function onCreate($attribute, $subject, TokenInterface $token): bool
    {
        return true;
    }

    /**
     * @param $attribute
     * @param TodoList $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function onDelete($attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        return $subject->isOwner($user);
    }
}
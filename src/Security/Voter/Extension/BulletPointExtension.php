<?php

namespace App\Security\Voter\Extension;

use App\Entity\Account\User;
use App\Entity\TodoList\BulletPoint;
use App\Entity\TodoList\TodoList;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class BulletPointExtension extends AbstractExtension implements VoterExtensionInterface
{

    #[Pure] public function __construct()
    {
        parent::__construct(BulletPoint::class);
    }

    /**
     * @param $attribute
     * @param BulletPoint $subject
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
     * @param BulletPoint $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function onUpdate($attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        return $subject->isModifier($user);
    }

    /**
     * @param $attribute
     * @param BulletPoint $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function onCreate($attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        return $subject->isModifier($user);
    }

    /**
     * @param $attribute
     * @param BulletPoint $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function onDelete($attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        return $subject->isModifier($user);
    }
}
<?php

namespace App\Security\Voter\Extension;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

interface VoterExtensionInterface
{
    public function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool;

    /**
     * @return class-string
     */
    public function getSupportedClass(): string;
}

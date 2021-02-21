<?php

namespace App\Security\Voter\Extension;

use http\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

abstract class AbstractExtension implements VoterExtensionInterface
{
    public function __construct(
        protected string $classname
    ) {
    }

    public function getSupportedClass(): string
    {
        /* @phpstan-ignore-next-line */
        return $this->classname;
    }

    public function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $func = 'on'.ucfirst($attribute);
        if (!method_exists($this, $func)) {
            throw new InvalidArgumentException('unknown function : '.$func);
        }

        return $this->{$func}($attribute, $subject, $token);
    }

    abstract protected function onRead(string $attribute, mixed $subject, TokenInterface $token): bool;

    abstract protected function onUpdate(string $attribute, mixed $subject, TokenInterface $token): bool;

    abstract protected function onCreate(string $attribute, mixed $subject, TokenInterface $token): bool;

    abstract protected function onDelete(string $attribute, mixed $subject, TokenInterface $token): bool;
}

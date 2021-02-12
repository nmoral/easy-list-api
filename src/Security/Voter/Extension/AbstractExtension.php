<?php


namespace App\Security\Voter\Extension;


use App\Entity\TodoList\TodoList;
use http\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

abstract class AbstractExtension
{
    /**
     * @param class-string $classname
     */
    public function __construct(
        protected string $classname
    )
    {}

    public function getSupportedClass(): string
    {
        return $this->classname;
    }


    /**
     * @param $attribute
     * @param TodoList $subject
     * @param TokenInterface $token
     * @return bool
     */
    public function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $func = 'on'.ucfirst($attribute);
        if(!method_exists($this, $func)) {
            throw new InvalidArgumentException('unknown function : '.$func);
        }

        return $this->{$func}($attribute, $subject, $token);
    }

    abstract protected function onRead($attribute, $subject, TokenInterface $token): bool;

    abstract protected function onUpdate($attribute, $subject, TokenInterface $token): bool;

    abstract protected function onCreate($attribute, $subject, TokenInterface $token): bool;

    abstract protected function onDelete($attribute, $subject, TokenInterface $token): bool;
}
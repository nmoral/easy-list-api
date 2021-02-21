<?php

namespace App\EventListener\ValidateSubscriber\Extension;

use App\Entity\Account\User;
use App\Event\PostValidateEvent;
use App\Event\PreValidateEvent;
use App\EventListener\AbstractExtensionListener;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserExtension extends AbstractExtensionListener implements ValidateSubscriberInterface
{
    public function __construct(
            private UserPasswordEncoderInterface $encoder
        ) {
        parent::__construct(User::class);
    }

    public function preValidate(PreValidateEvent $event): void
    {
    }

    public function postValidate(PostValidateEvent $event): void
    {
        /** @var User $object */
        $object = $event->getEntity();
        if (null !== $object->getId()) {
            return;
        }

        $encodedPassword = $this->encoder->encodePassword($object, $object->getPlainPassword());
        $object
            ->setPassword($encodedPassword)
            ->eraseCredentials();
    }
}

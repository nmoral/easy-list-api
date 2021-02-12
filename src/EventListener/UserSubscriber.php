<?php


namespace App\EventListener;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Account\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserSubscriber implements EventSubscriberInterface
{
    public function __construct(private UserPasswordEncoderInterface $encoder)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['preWrite', EventPriorities::PRE_WRITE],
        ];
    }

    public function preWrite(ViewEvent $event)
    {
        if (
            Request::METHOD_POST !== $event->getRequest()->getMethod()
            && Request::METHOD_PUT !== $event->getRequest()->getMethod()
        ) {
            return;
        }

        $object = $event->getControllerResult();
        if (!$object instanceof User) {
            return;
        }

        $encodedPassword = $this->encoder->encodePassword($object, $object->getPlainPassword());
        $object
            ->setPassword($encodedPassword)
            ->eraseCredentials();
    }
}
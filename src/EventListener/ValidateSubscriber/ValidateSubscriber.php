<?php

namespace App\EventListener\ValidateSubscriber;

use App\Event\Event;
use App\Event\PostValidateEvent;
use App\Event\PreValidateEvent;
use App\Event\ValidateEvent;
use App\Exception\ViolationException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidateSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Event::VALIDATE_EVENT => 'validate',
        ];
    }

    /**
     * @throws ViolationException
     */
    public function validate(ValidateEvent $event): void
    {
        if (!in_array($event->getRequest()->getMethod(), [Request::METHOD_POST, Request::METHOD_PUT], true)) {
            return;
        }
        $this->eventDispatcher->dispatch(new PreValidateEvent($event->getRequest(), $event->getEntity()), Event::PRE_VALIDATE_EVENT);
        $violations = $this->validator->validate($event->getEntity());

        if ($violations->count()) {
            throw new ViolationException($violations);
        }
        $this->eventDispatcher->dispatch(new PostValidateEvent($event->getRequest(), $event->getEntity()), Event::POST_VALIDATE_EVENT);
    }
}

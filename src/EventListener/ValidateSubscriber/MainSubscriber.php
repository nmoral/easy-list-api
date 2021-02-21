<?php

namespace App\EventListener\ValidateSubscriber;

use App\Entity\AbstractEntity;
use App\Event\Event;
use App\Event\PostValidateEvent;
use App\Event\PreValidateEvent;
use App\EventListener\ValidateSubscriber\Extension\ValidateSubscriberInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MainSubscriber implements EventSubscriberInterface
{
    /** @var ValidateSubscriberInterface[] */
    private array $extensions;

    public function __construct()
    {
        $this->extensions = [];
    }

    public function addExtension(ValidateSubscriberInterface $subscriber): void
    {
        $this->extensions[$subscriber->getSupportedClass()] = $subscriber;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Event::PRE_VALIDATE_EVENT => 'preValidate',
            Event::POST_VALIDATE_EVENT => 'postValidate',
        ];
    }

    public function preValidate(PreValidateEvent $event): void
    {
        $subject = $event->getEntity();
        if (!$this->support($subject)) {
            return;
        }

        $this->extensions[get_class($subject)]->preValidate($event);
    }

    public function postValidate(PostValidateEvent $event): void
    {
        $subject = $event->getEntity();
        if (!$this->support($subject)) {
            return;
        }

        $this->extensions[get_class($subject)]->postValidate($event);
    }

    private function support(AbstractEntity $subject): bool
    {
        return isset($this->extensions[get_class($subject)]);
    }
}

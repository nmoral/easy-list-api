<?php


namespace App\EventListener\ValidateSubscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\EventListener\ValidateSubscriber\Extension\ValidateSubscriberInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class MainSubscriber implements EventSubscriberInterface
{
    public function __construct(
        /** @var ValidateSubscriberInterface[] $extensions */
        private array $extensions = []
    )
    {}

    public function addExtension(ValidateSubscriberInterface $subscriber)
    {
        $this->extensions[$subscriber->getSupportedClass()] = $subscriber;
    }


    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [
                'preValidate', EventPriorities::PRE_VALIDATE,
                'postValidate', EventPriorities::POST_VALIDATE,
            ],
        ];
    }

    public function preValidate(ViewEvent $event)
    {
        $subject = $event->getControllerResult();
        if (!$this->support($subject)) {
            return;
        }

        $this->extensions[get_class($subject)]->preValidate($event);
    }

    public function postValidate(ViewEvent $event)
    {
        $subject = $event->getControllerResult();
        if (!$this->support($subject)) {
            return;
        }

        $this->extensions[get_class($subject)]->postValidate($event);
    }

    private function support($subject): bool
    {
        return isset($this->extensions[get_class($subject)]);
    }
}
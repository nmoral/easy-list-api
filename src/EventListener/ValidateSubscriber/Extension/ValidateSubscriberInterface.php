<?php

namespace App\EventListener\ValidateSubscriber\Extension;

use App\EventListener\SubscriberExtensionInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;

interface ValidateSubscriberInterface extends SubscriberExtensionInterface
{
    public function preValidate(ViewEvent $event);

    public function postValidate(ViewEvent $event);
}
<?php

namespace App\EventListener\ValidateSubscriber\Extension;

use App\Event\PostValidateEvent;
use App\Event\PreValidateEvent;
use App\EventListener\SubscriberExtensionInterface;

interface ValidateSubscriberInterface extends SubscriberExtensionInterface
{
    public function preValidate(PreValidateEvent $event): void;

    public function postValidate(PostValidateEvent $event): void;
}

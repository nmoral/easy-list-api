<?php

namespace App\EventListener;

interface SubscriberExtensionInterface
{
    /**
     * @return class-string
     */
    public function getSupportedClass(): string;
}

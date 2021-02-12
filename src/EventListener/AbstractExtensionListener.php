<?php


namespace App\EventListener;

class AbstractExtensionListener implements SubscriberExtensionInterface
{
    public function __construct(
        private string $supportedClass
    )
    {}

    public function getSupportedClass(): string
    {
        return $this->supportedClass;
    }

}
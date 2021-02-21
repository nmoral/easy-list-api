<?php

namespace App\EventListener;

class AbstractExtensionListener implements SubscriberExtensionInterface
{
    public function __construct(
        private string $supportedClass
    ) {
    }

    /**
     * @return class-string
     */
    public function getSupportedClass(): string
    {
        /* @phpstan-ignore-next-line */
        return $this->supportedClass;
    }
}

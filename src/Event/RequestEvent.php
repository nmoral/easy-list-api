<?php

declare(strict_types=1);

namespace App\Event;

use Symfony\Component\HttpFoundation\Request;

abstract class RequestEvent
{
    public function __construct(
        private Request $request
    ) {
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}

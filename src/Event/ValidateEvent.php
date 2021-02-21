<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\AbstractEntity;
use Symfony\Component\HttpFoundation\Request;

final class ValidateEvent extends RequestEvent
{
    public function __construct(
            Request $request,
            private AbstractEntity $entity
        ) {
        parent::__construct($request);
    }

    public function getEntity(): AbstractEntity
    {
        return $this->entity;
    }
}

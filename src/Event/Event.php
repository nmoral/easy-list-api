<?php

declare(strict_types=1);

namespace App\Event;

interface Event
{
    public const VALIDATE_EVENT = 'event.validate';

    public const PRE_VALIDATE_EVENT = 'event.pre_validate';

    public const POST_VALIDATE_EVENT = 'event.post_validate';
}

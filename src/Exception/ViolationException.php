<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ViolationException extends \Exception
{
    public function __construct(
            private ConstraintViolationListInterface $violations
        ) {
        parent::__construct($this->createMessage(), 400);
    }

    private function createMessage(): string
    {
        $message = [];
        /**
         * @var ConstraintViolationInterface $violation
         */
        foreach ($this->violations as $field => $violation) {
            $message[] = $violation->getPropertyPath().' : '.$violation->getMessage();
        }

        return implode(\PHP_EOL, $message);
    }
}

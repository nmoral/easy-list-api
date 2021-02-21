<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Serializer\CachedGroupResolver;
use App\Service\Serialization\GroupGenerator;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class AbstractKernelSubscriber
{
    public function __construct(
        private CachedGroupResolver $groupResolver,
    ) {
    }

    /**
     * @param class-string $className
     *
     * @return mixed[]
     *
     * @throws \ReflectionException
     */
    protected function generateContext(string $className, string $method): array
    {
        return [
            AbstractNormalizer::GROUPS => GroupGenerator::generateGroups($className, $method),
            AbstractNormalizer::ATTRIBUTES => $this->groupResolver->resolve($className, $method),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Cache\Serialization;

use App\Controller\AbstractApiController;
use App\Serializer\CachedGroupResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class ClassWarmer implements CacheWarmerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CachedGroupResolver $resolver
    ) {
    }

    public function isOptional(): bool
    {
        return false;
    }

    /**
     * @return string[]
     */
    public function warmUp(string $cacheDir): array
    {
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        foreach ($metadata as $metadatum) {
            /** @var class-string $className */
            $className = $metadatum->getName();
            $this->generateCacheForClass($className);
        }

        return [];
    }

    /**
     * @param class-string $className
     *
     * @throws \ReflectionException
     */
    private function generateCacheForClass(string $className): void
    {
        foreach (AbstractApiController::ACTION as $action) {
            $this->resolver->resolve($className, $action, true);
        }
    }
}

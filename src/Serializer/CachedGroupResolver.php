<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Service\Serialization\GroupGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class CachedGroupResolver extends GroupResolver
{
    public const CLASS_CACHE_PREFIX = 'serialization.classes.groups';

    public function __construct(
        EntityManagerInterface $entityManager,
        private AdapterInterface $cache
    ) {
        parent::__construct($entityManager);
    }

    public static function getItemKey(string $groupName): string
    {
        return sprintf('%s.%s', self::CLASS_CACHE_PREFIX, $groupName);
    }

    public function resolve(string $class, string $action, bool $refreshCache = false): array
    {
        $groupName = GroupGenerator::generateGroup($class, $action);
        $itemKey = self::getItemKey($groupName);
        /** @var CacheItemInterface $cacheItem */
        $cacheItem = $this->cache->getItem($itemKey);
        if (!$cacheItem->isHit() || $refreshCache) {
            $cacheItem->set(parent::resolve($class, $action));
            $this->cache->save($cacheItem);
        }

        return $cacheItem->get();
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Serializer;

use App\Serializer\CachedGroupResolver;
use App\Service\Serialization\GroupGenerator;
use App\Tests\Resources\Entity\TestUser;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\TraceableAdapter;
use Symfony\Contracts\Cache\CacheInterface;

class CachedGroupResolverTest extends KernelTestCase
{
    private CachedGroupResolver $groupResolver;

    public function setUp(): void
    {
        self::bootKernel();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::$container->get('doctrine.orm.entity_manager');
        /** @var AdapterInterface $cache */
        $cache = self::$container->get(AdapterInterface::class);

        $this->groupResolver = new CachedGroupResolver($entityManager, $cache);
    }

    public function testGetGroupName(): void
    {
        $group = GroupGenerator::generateGroup(TestUser::class, 'create');
        $key = CachedGroupResolver::getItemKey($group);
        self::assertSame(CachedGroupResolver::CLASS_CACHE_PREFIX.'.'.$group, $key);
    }

    public function testCache(): void
    {
        /** @var TraceableAdapter $cache */
        $cache = self::$container->get(CacheInterface::class);
        $group = GroupGenerator::generateGroup(TestUser::class, 'create');
        /** @var CacheItemInterface $item */
        $item = $cache->getItem(CachedGroupResolver::getItemKey($group));
        self::assertTrue($item->isHit());
    }
}

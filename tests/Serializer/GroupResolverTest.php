<?php

declare(strict_types=1);

namespace App\Tests\Serializer;

use App\Serializer\GroupResolver;
use App\Tests\Resources\Entity\TestUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GroupResolverTest extends KernelTestCase
{
    private GroupResolver $groupResolver;

    public function setUp(): void
    {
        self::bootKernel();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::$container->get('doctrine.orm.entity_manager');

        $this->groupResolver = new GroupResolver($entityManager);
    }

    public function testGroupResolving(): void
    {
        $fields = $this->groupResolver->resolve(TestUser::class, 'create');
        self::assertSame([
            'id',
            'name',
            'item' => ['name'],
            'parentAttr',
        ], $fields);
    }
}

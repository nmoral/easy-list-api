<?php

declare(strict_types=1);

namespace App\Tests\Service\Serialization;

use App\Entity\Account\User;
use App\Serializer\GroupResolver;
use App\Service\Serialization\GroupGenerator;
use PHPUnit\Framework\TestCase;

class GroupGeneratorTest extends TestCase
{
    public function testGroupGeneration(): void
    {
        $className = User::class;
        $action = 'list';

        self::assertSame('account_user_list', GroupGenerator::generateGroup($className, $action));
    }

    public function testExceptionOnWrongClassForm(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $className = '';
        $action = 'list';

        $r = GroupGenerator::generateGroup($className, 'list');
    }

    public function testEntityGroups(): void
    {
        $className = User::class;
        $action = 'list';

        self::assertSame([GroupResolver::DEFAULT_GROUP, 'account_user_list'], GroupGenerator::generateGroups($className, $action));
    }
}

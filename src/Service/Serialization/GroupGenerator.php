<?php

declare(strict_types=1);

namespace App\Service\Serialization;

use App\Serializer\GroupResolver;

final class GroupGenerator
{
    private const NAMESPACE_SEPARATOR = '\\';

    public static function generateGroup(string $className, string $action): string
    {
        return strtolower(self::generateClassPart($className).$action);
    }

    /**
     * @return string[]
     */
    public static function generateGroups(string $className, string $action): array
    {
        return [
            GroupResolver::DEFAULT_GROUP,
            self::generateGroup($className, $action),
        ];
    }

    private static function generateClassPart(string $className): string
    {
        $parts = explode(self::NAMESPACE_SEPARATOR, $className);
        /** @var string|null $shortName */
        $shortName = array_pop($parts);
        /** @var string|null $lastDirName */
        $lastDirName = array_pop($parts);

        if (null === $shortName || null === $lastDirName) {
            throw new \InvalidArgumentException('unable to define classPartName');
        }

        return sprintf('%s_%s_', $lastDirName, $shortName);
    }
}

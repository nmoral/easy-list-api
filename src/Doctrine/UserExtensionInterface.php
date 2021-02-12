<?php

namespace App\Doctrine;

use Doctrine\ORM\QueryBuilder;

interface UserExtensionInterface
{
    public function getSupportedClass(): string;

    public function addWhere(QueryBuilder $queryBuilder);
}
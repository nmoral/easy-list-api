<?php

namespace App\Doctrine\Extensions;

use App\Doctrine\UserExtensionInterface;
use App\Entity\TodoList\TodoList;
use Doctrine\ORM\QueryBuilder;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\Security;

final class TodoListExtension extends AbstractUserExtension implements UserExtensionInterface
{
    #[Pure] public function __construct(Security $security)
    {
        parent::__construct($security, TodoList::class);
    }

    public function addWhere(QueryBuilder $queryBuilder): void
    {

        $user = $this->getUser();
        if (null === $user) {
            return;
        }
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->innerJoin(sprintf('%s.listManagers', $rootAlias), 'lm')
            ->innerJoin('lm.user', 'u')
            ->andWhere('u.id = :user')
            ->setParameter('user', $user->getId())
        ;
    }
}
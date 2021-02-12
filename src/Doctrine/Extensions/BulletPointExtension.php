<?php

namespace App\Doctrine\Extensions;

use App\Doctrine\UserExtensionInterface;
use App\Entity\TodoList\BulletPoint;
use App\Entity\TodoList\TodoList;
use Doctrine\ORM\QueryBuilder;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\Security;

final class BulletPointExtension extends AbstractUserExtension implements UserExtensionInterface
{
    #[Pure] public function __construct(Security $security)
    {
        parent::__construct($security, BulletPoint::class);
    }

    public function addWhere(QueryBuilder $queryBuilder): void
    {

        $user = $this->getUser();
        if (null === $user) {
            return;
        }
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->innerJoin(sprintf('%s.todoList', $rootAlias),'tl')
            ->innerJoin('tl.listManagers', 'lm')
            ->innerJoin('lm.user', 'u')
            ->andWhere('u.id = :user')
            ->setParameter('user', $user->getId())
        ;
    }
}
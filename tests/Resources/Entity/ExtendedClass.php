<?php

declare(strict_types=1);

namespace App\Tests\Resources\Entity;

use App\Serializer\GroupResolver;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\MappedSuperclass
 */
class ExtendedClass
{
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    #[Groups([GroupResolver::DEFAULT_GROUP])]
    public int $parentAttr = 0;
}

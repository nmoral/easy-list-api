<?php

declare(strict_types=1);

namespace App\Tests\Resources\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(schema="public")
 */
class TestUser extends ExtendedClass
{
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    #[Groups(['default'])]
    protected ?int $id = null;

    /**
     * @ORM\Column(type="string")
     */
    #[Groups(['entity_testuser_create'])]
    public ?string $name;

    /**
     * @ORM\OneToOne(targetEntity="App\Tests\Resources\Entity\TestItem", mappedBy="user")
     */
    #[Groups(['entity_testuser_create'])]
    public TestItem $item;
}

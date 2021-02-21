<?php

declare(strict_types=1);

namespace App\Tests\Resources\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(schema="public")
 */
class TestItem
{
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected ?int $id = null;

    /**
     * @ORM\OneToOne(targetEntity="App\Tests\Resources\Entity\TestUser", inversedBy="item")
     */
    #[Groups(['entity_testuser_create', 'default'])]
    public TestUser $user;

    /**
     * @ORM\Column(type="string")
     */
    #[Groups(['entity_testuser_create'])]
    public string $name;
}

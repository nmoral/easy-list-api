<?php

namespace App\Entity\TodoList;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Account\User;
use App\Entity\AbstractEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table
 *
 * @ApiResource(
 *     collectionOperations= {
 *          "post": {
 *              "denormalization_context": {
 *                  "groups": {
 *                      "bullet_point_create"
 *                  }
 *              },
 *              "normalization_context": {
 *                  "groups": {
 *                      "bullet_point_details", "details"
 *                  }
 *              }
 *          },
 *          "get": {
 *              "normalization_context": {
 *                  "groups": {
 *                      "bullet_point_list", "list"
 *                  }
 *              }
 *          }
 *      },
 *     itemOperations= {
 *          "put": {
 *              "denormalization_context": {
 *                  "groups": {
 *                      "bullet_point_edit", "edit"
 *                  }
 *              },
 *              "normalization_context": {
 *                  "groups": {
 *                      "bullet_point_details", "details"
 *                  }
 *              }
 *          },
 *          "get": {
 *              "normalization_context": {
 *                  "groups": {
 *                      "bullet_point_details", "details"
 *                  }
 *              }
 *          },
 *          "delete"
 *     }
 * )
 */
class BulletPoint extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     *
     * @Groups({
     *     "details", "list",
     *     "bullet_point_create"
     * })
     */
    private string $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Groups({
     *     "details",
     *     "bullet_point_create"
     * })
     */
    private string $description;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Groups({
     *     "details", "list",
     *     "bullet_point_create"
     * })
     */
    private \DateTimeInterface $dueDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TodoList\TodoList", inversedBy="bulletPoints")
     *
     * @Groups({
     *     "bullet_point_list",
     *     "bullet_point_create"
     * })
     */
    private TodoList $todoList;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDueDate(): \DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate(\DateTimeInterface $dueDate): self
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function getTodoList(): TodoList
    {
        return $this->todoList;
    }

    public function setTodoList(TodoList $todoList): self
    {
        $this->todoList = $todoList;

        return $this;
    }

    public function isWatcher(User $user): bool
    {
        return $this->todoList->isWatcher($user);
    }

    public function isModifier(User $user): bool
    {
        return $this->todoList->isModifier($user);
    }
}

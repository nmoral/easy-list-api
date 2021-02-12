<?php


namespace App\Entity\TodoList;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\AbstractEntity;
use App\Entity\Account\User;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     uniqueConstraints=@ORM\UniqueConstraint(
 *         name="list_manager_unique_constraint",
 *         columns={"list_id", "user_id"}
 *     )
 * )
 *
 * @UniqueEntity(
 *     fields={"list", "user"},
 *     errorPath="user",
 *     message="This value is already used."
 * )
 *
 * @ApiResource(
 *     collectionOperations= {
 *          "post": {
 *              "denormalization_context": {
 *                  "groups": {
 *                      "list_manager_create"
 *                  }
 *              },
 *              "normalization_context": {
 *                  "groups": {
 *                      "list_manager_details", "details"
 *                  }
 *              }
 *          },
 *          "get": {
 *              "normalization_context": {
 *                  "groups": {
 *                      "list_manager_list", "list"
 *                  }
 *              }
 *          }
 *      },
 *     itemOperations= {
 *          "put": {
 *              "denormalization_context": {
 *                  "groups": {
 *                      "list_manager_edit", "edit"
 *                  }
 *              },
 *              "normalization_context": {
 *                  "groups": {
 *                      "list_manager_details", "details"
 *                  }
 *              }
 *          },
 *          "get": {
 *              "normalization_context": {
 *                  "groups": {
 *                      "list_manager_details", "details"
 *                  }
 *              }
 *          },
 *          "delete"
 *     }
 * )
 */
class ListManager extends AbstractEntity
{
    public const STATUS_OWNER = 'OWNER';

    public const STATUS_MODIFIER = 'MODIFIER';

    public const STATUS_VIEWER = 'VIEW';

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TodoList\TodoList", inversedBy="listManagers")
     */
    private TodoList $list;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Account\User", inversedBy="managedLists")
     */
    private User $user;

    /**
     * @ORM\Column(type="string", length=10, nullable=false)
     */
    private string $status;

    public function getList(): TodoList
    {
        return $this->list;
    }

    public function setList(TodoList $list): self
    {
        $this->list = $list;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    #[Pure] public function hasRight(User $user, array $right): bool
    {
        return $this->getUser() === $user && in_array($this->getStatus(), $right, true);
    }
}
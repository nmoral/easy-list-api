<?php

namespace App\Entity\TodoList;

use App\Entity\AbstractEntity;
use App\Entity\Account\User;
use Doctrine\ORM\Mapping as ORM;
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

    /**
     * @param string[] $right
     */
    public function hasRight(User $user, array $right): bool
    {
        return $this->getUser() === $user && in_array($this->getStatus(), $right, true);
    }
}

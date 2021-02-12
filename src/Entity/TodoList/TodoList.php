<?php

namespace App\Entity\TodoList;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Account\User;
use App\Entity\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class TodoList.
 *
 * @ORM\Entity
 * @ORM\Table
 *
 * @ApiResource(
 *     collectionOperations= {
 *          "post": {
 *              "denormalization_context": {
 *                  "groups": {
 *                      "todo_list_create"
 *                  }
 *              },
 *              "normalization_context": {
 *                  "groups": {
 *                      "todo_list_details", "details"
 *                  }
 *              }
 *          },
 *          "get": {
 *              "normalization_context": {
 *                  "groups": {
 *                      "todo_list_list", "list"
 *                  }
 *              }
 *          }
 *      },
 *     itemOperations= {
 *          "put": {
 *              "denormalization_context": {
 *                  "groups": {
 *                      "todo_list_edit", "edit"
 *                  }
 *              },
 *              "normalization_context": {
 *                  "groups": {
 *                      "todo_list_details", "details"
 *                  }
 *              }
 *          },
 *          "get": {
 *              "normalization_context": {
 *                  "groups": {
 *                      "todo_list_details", "details"
 *                  }
 *              }
 *          },
 *          "delete"
 *     }
 * )
 */
class TodoList extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     * @Assert\Type(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(max=100)
     * @Groups({
     *     "details", "list",
     *     "todo_list_create",
     *     "todo_list_edit"
     * })
     */
    private string $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Type(type="string")
     * @Assert\Length(max=2000)
     * @Assert\NotBlank()
     * @Groups({
     *     "details", "list",
     *     "todo_list_create",
     *     "todo_list_edit"
     * })
     */
    private ?string $description;

    /**
     * @var Collection<BulletPoint>
     * @ORM\OneToMany(targetEntity="App\Entity\TodoList\BulletPoint", mappedBy="todoList")
     *
     * @Groups({
     *     "details"
     * })
     */
    private Collection $bulletPoints;

    /**
     * @var Collection<ListManager>
     * @ORM\OneToMany(targetEntity="App\Entity\TodoList\ListManager", mappedBy="list", cascade={"persist", "remove"})
     * @Assert\Valid
     */
    private Collection $listManagers;

    #[Pure] public function __construct()
    {
        $this->bulletPoints = new ArrayCollection();
        $this->listManagers = new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getBulletPoints(): Collection
    {
        return $this->bulletPoints;
    }

    public function addBulletPoint(BulletPoint $point): self
    {
        if (!$this->bulletPoints->contains($point)) {
            $this->bulletPoints[] = $point;
        }

        return $this;
    }

    public function removeBulletPoint(BulletPoint $point): self
    {
        if ($this->bulletPoints->contains($point)) {
            $this->bulletPoints->removeElement($point);
        }

        return $this;
    }

    public function addListManager(ListManager $manager): self
    {
        if (!$this->listManagers->contains($manager)) {
            $this->listManagers[] = $manager;
            $manager->setList($this);
        }

        return $this;
    }

    public function removeListManager(ListManager $manager): self
    {
        if ($this->listManagers->contains($manager)) {
            $this->listManagers->removeElement($manager);
        }

        return $this;
    }

    public function getListManagers(): Collection
    {
        return $this->listManagers;
    }

    public function isWatcher(User $user): bool
    {
        $filteredCollection = $this->filterBy($user, [
            ListManager::STATUS_OWNER,
            ListManager::STATUS_MODIFIER,
            ListManager::STATUS_VIEWER,
        ]);

        return 0 !== $filteredCollection->count();
    }

    public function isModifier(User $user): bool
    {
        $filteredCollection = $this->filterBy($user, [
            ListManager::STATUS_OWNER,
            ListManager::STATUS_MODIFIER,
        ]);

        return 0 !== $filteredCollection->count();

    }

    public function isOwner(User $user): bool
    {
        $filteredCollection = $this->filterBy($user, [
            ListManager::STATUS_OWNER
        ]);

        return 0 !== $filteredCollection->count();

    }

    private function filterBy(User $user, array $right = []): Collection
    {
        return $this->getListManagers()->filter(function(ListManager $manager) use ($user, $right) {
            return $manager->hasRight($user, $right);
        });
    }
}

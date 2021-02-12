<?php


namespace App\Entity\Account;


use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\AbstractEntity;
use App\Entity\TodoList\ListManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table
 *
 * @ApiResource(
 *     collectionOperations= {
 *          "newAccount": {
 *             "method": "POST",
 *             "path": "/new_account",
 *             "controller": App\Action\Collection\User\CreateAction::class,
 *             "denormalization_context": {
 *                 "groups": {"user_create"}
 *             },
 *             "normalization_context": {
 *                 "groups": {"user_details", "details"}
 *             }
 *         },
 *          "get": {
 *              "normalization_context": {
 *                  "groups": {
 *                      "user_list", "list"
 *                  }
 *              }
 *          }
 *      },
 *     itemOperations= {
 *          "put": {
 *              "denormalization_context": {
 *                  "groups": {
 *                      "user_edit", "edit"
 *                  }
 *              },
 *              "normalization_context": {
 *                  "groups": {
 *                      "user_details", "details"
 *                  }
 *              }
 *          },
 *          "get": {
 *              "normalization_context": {
 *                  "groups": {
 *                      "user_details", "details"
 *                  }
 *              }
 *          },
 *          "delete"
 *     }
 * )
 */
class User extends AbstractEntity implements UserInterface
{
    /**
     * @var array<int, string>
     * @ORM\Column(type="array", nullable=false)
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="string", length=250)
     */
    private string $password;

    private ?string $plainPassword;

    /**
     * @ORM\Column(type="string", nullable=false, length=50)
     */
    private string $username;

    /**
     * @var Collection<ListManager>
     * @ORM\OneToMany(targetEntity="App\Entity\TodoList\ListManager", mappedBy="user")
     */
    private Collection $managedLists;

    public function __construct()
    {
        $this->managedLists = new ArrayCollection();
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function addManagedList(ListManager $list): self
    {
        if (!$this->managedLists->contains($list)) {
            $this->managedLists[] = $list;
        }

        return $this;
    }

    public function removeManagedList(ListManager $list): self
    {
        if ($this->managedLists->contains($list)) {
            $this->managedLists->removeElement($list);
        }

        return $this;
    }

    public function getManagedLists(): ArrayCollection|Collection
    {
        return $this->managedLists;
    }

    public static function createFromJson(string $json): self
    {
        $formatted = \json_decode($json, associative: true);
        if (null === $formatted) {
            throw new BadRequestHttpException(message: 'Unable to parse content');
        }
        $user = new User();
        $user
            ->setPlainPassword($formatted['plainPassword'])
            ->setUsername($formatted['username'])
        ;

        return $user;
    }
}
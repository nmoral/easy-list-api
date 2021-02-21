<?php

namespace App\Entity\Account;

use App\Entity\AbstractEntity;
use App\Entity\TodoList\ListManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(schema="public")
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

    /**
     * @var string|null
     */
    #[Groups([
        'account_user_create',
    ])]
    private ?string $plainPassword;

    /**
     * @ORM\Column(type="string", nullable=false, length=50)
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    #[Groups([
        'account_user_create',
        'account_user_edit',
        'account_user_getitem',
    ])]
    private string $username;

    /**
     * @var Collection<int, ListManager>
     * @ORM\OneToMany(targetEntity="App\Entity\TodoList\ListManager", mappedBy="user")
     */
    private Collection $managedLists;

    public function __construct()
    {
        $this->managedLists = new ArrayCollection();
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * @param string[] $roles
     *
     * @return $this
     */
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

    /**
     * @return Collection<int, ListManager>
     */
    public function getManagedLists(): Collection
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

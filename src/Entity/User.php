<?php
/**
 * User
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(
 *     name="user",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(
 *              name="username_idx",
 *              columns={"username"},
 *          )
 *     }
 * )
 *
 * @UniqueEntity(fields={"username"})
 */
class User implements UserInterface
{
    /**
     * Role user.
     *
     * @var string
     */
    const ROLE_USER = 'ROLE_USER';

    /**
     * Role admin.
     * @var string
     */
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * Primary key.
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(
     *     name="id",
     *     type="integer",
     *     nullable=false,
     *     options={"unsigned"=true},
     * )
     */
    private $id;

    /**
     * Username
     *
     * @var string
     *
     * @ORM\Column(type="string", length=64)
     */
    private $username;

    /**
     * The hashed password.
     * @var string
     *
     * @ORM\Column(type="string")

     * @Assert\NotBlank
     * @Assert\Type(type="string")
     *
     * @SecurityAssert\UserPassword
     */
    private $password;

    /**
     * Roles.
     *
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity=Album::class, mappedBy="user")
     */
    private $albums;

    /**
     * @var UserData
     *
     * @ORM\OneToOne(targetEntity=UserData::class, inversedBy="user", cascade={"persist", "remove"})
     */
    private $profile;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->isActive = true;
        $this->albums = new ArrayCollection();
    }

    /**
     * Getter for the Id.
     *
     * @return int Result
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     *
     * @return string User name
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * Getter for the Roles.
     *
     * @see UserInterface
     *
     * @return array Roles
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = static::ROLE_USER;

        return array_unique($roles);
    }

    /**
     * Setter for the Roles.
     *
     * @param array $roles Roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * Getter for the Password.
     *
     * @see UserInterface
     *
     * @return string|null Password
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    /**
     * Setter for the Password.
     *
     * @param string $password Password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @return Collection
     */
    public function getAlbums()
    {
        return $this->albums;
    }

    /**
     * @param Album $album
     * @return $this
     */
    public function addAlbum(Album $album): self
    {
        if (!$this->albums->contains($album)) {
            $this->albums[] = $album;
            $album->setUser($this);
        }

        return $this;
    }

    /**
     * @param Album $album
     * @return $this
     */
    public function removeAlbum(Album $album): self
    {
        if ($this->albums->removeElement($album)) {
            // set the owning side to null (unless already changed)
            if ($album->getUser() === $this) {
                $album->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return UserData
     */
    public function getProfile(): UserData
    {
        return $this->profile;
    }

    /**
     * @param UserData $profile
     */
    public function setProfile(UserData $profile): void
    {
        $profile->setUser($this);
        $this->profile = $profile;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}

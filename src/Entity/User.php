<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\{ PasswordAuthenticatedUserInterface, UserInterface };
use App\Entity\City;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 8)]
    #[Assert\NotBlank(message: 'El DNI es obligatorio.')]
    #[Assert\Regex(
        pattern: '/^\d+$/',
        message: 'El DNI solo puede contener números sin espacios ni símbolos.'
    )]
    private ?string $dni = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageProfile = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $city = null;

    #[ORM\Column(type: 'boolean', options:['default' => false])]
    private bool $isOnline = false;

    #[ORM\Column(type: 'boolean', options:['default' => false])]
    private bool $isSuspended = false;

    #[ORM\Column(type: 'boolean', options:['default' => false])]
    private bool $isDeleted = false;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private bool $isVerified = false;

    // Agregamos el campo verificationToken
    #[ORM\Column(nullable: true)]
    private ?string $verificationToken = null;

    #[ORM\ManyToMany(targetEntity:WorkTeam::class, inversedBy: 'users')]
    #[ORM\JoinTable(name: 'user_work_team')]
    private collection $workTeams;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // Garantiza que cada usuario tenga al menos ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // Si almacenas datos sensibles aquí, límpialos
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getVerificationToken(): ?string
    {
        return $this->verificationToken;
    }

    public function setVerificationToken(?string $verificationToken): static
    {
        $this->verificationToken = $verificationToken;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }
    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }
    public function getLastname(): ?string
    {
        return $this->lastname;
    }
    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getDni(): ?string
    {
        return $this->dni;
    }
    
    public function setDni(?string $dni): static
    {
        $this->dni = $dni;

        return $this;
    }

    public function getImageProfile(): ?string
    {
        return $this->imageProfile;
    }
    public function setImageProfile(?string $imageProfile): static
    {
        $this->imageProfile = $imageProfile;

        return $this;
    }
    public function getCity(): ?City
    {
        return $this->city;
    }
    public function setCity(?City $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getIsOnline(): ?bool
    {
        return $this->isOnline;
    }

    public function setIsOnline(bool $isOnline): static
    {
        $this->isOnline = $isOnline;

        return $this;
    }

    public function getIsSuspended(): bool
    {
        return $this->isSuspended;
    }

    public function setIsSuspended(bool $isSuspended): static
    {
        $this->isSuspended = $isSuspended;
        return $this;
    }

    public function getIsDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): static
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function __construct()
    {
        $this->workTeams = new ArrayCollection();
    }
    public function getWorkTeams(): Collection
    {
        return $this->workTeams;
    }
    public function addWorkTeam(Workteam $workTeam): static
    {
        if (!$this->workTeams->contains($workTeam)) {
            $this->workTeams[] = $workTeam;
            $workTeam->addUser($this);
        }

        return $this;
    }
    public function removeWorkTeam(Workteam $workTeam): static
    {
        if ($this->workTeams->removeElement($workTeam)) {
            $workTeam->removeUser($this);
        }

        return $this;
    }
}

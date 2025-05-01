<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
class Course
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length:255)]
    private ?string $name = null;

    #[ORM\Column(length:50)]
    private ?string $prefix = null;

    #[ORM\Column(type:'boolean')]
    private bool $active = false;

    /*#[ORM\OneToMany(mappedBy: 'curso', targetEntity: Group::class, cascade: ['persist', 'remove'])]
    private collection $group;*/

    #[ORM\Column(length: 100)]
    private ?string $implementation_mode;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: 'La fecha de inicio es obligatoria')]
    private ?\DateTimeInterface $init_date = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $end_date = null; // Nueva propiedad

    #[ORM\Column(type: 'integer')]
    private int $hours; // Propiedad ya existente

    /*#[ORM\ManyToOne(targetEntity: Autoridad::class, inversedBy: 'cursos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Autoridad $enforcement_authority = null; // Relación con Autoridad*/

    #[ORM\Column(type: 'string', length: 100)]
    private string $axis; // Valores: Fortalecimiento o Profundización

    #[ORM\Column(type: 'boolean')]
    private bool $assessment = false; // Evaluación (si o no)

    #[ORM\Column(type: 'integer')]
    private int $credits; // Créditos


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }
    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }
    
    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /*public function getGroup(): Collection
    {
        return $this->group;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->group->contains($group)) {
            $this->group[] = $group;
            $group->setCurso($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->group->removeElement($group)) {
            // set the owning side to null (unless already changed)
            if ($group->getCurso() === $this) {
                $group->setCurso(null);
            }
        }

        return $this;
    }*/

    public function getImplementationMode(): ?string
    {
        return $this->implementation_mode;
    }

    public function setImplementationMode(string $implementation_mode): self
    {
        $this->implementation_mode = $implementation_mode;

        return $this;
    }

    public function getInitDate(): ?\DateTimeInterface
    {
        return $this->init_date;
    }

    public function setInitDate(\DateTimeInterface $init_date): self
    {
        $this->init_date = $init_date;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->end_date;
    }

    public function setEndDate(?\DateTimeInterface $end_date): self
    {
        $this->end_date = $end_date;

        return $this;
    }

    public function getHours(): int
    {
        return $this->hours;
    }

    public function setHours(int $hours): self
    {
        $this->hours = $hours;

        return $this;
    }

    /*public function getEnforcementAuthority(): ?Autoridad
    {
        return $this->enforcement_authority;
    }

    public function setEnforcementAuthority(?Autoridad $enforcement_authority): self
    {
        $this->enforcement_authority = $enforcement_authority;

        return $this;
    }*/

    public function getAxis(): string
    {
        return $this->axis;
    }

    public function setAxis(string $axis): self
    {
        $this->axis = $axis;

        return $this;
    }

    public function isAssessment(): bool
    {
        return $this->assessment;
    }

    public function setAssessment(bool $assessment): self
    {
        $this->assessment = $assessment;

        return $this;
    }

    public function getCredits(): int
    {
        return $this->credits;
    }

    public function setCredits(int $credits): self
    {
        $this->credits = $credits;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

}

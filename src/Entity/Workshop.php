<?php

namespace App\Entity;

use App\Repository\WorkshopRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkshopRepository::class)]
class Workshop
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    // Nuevo campo: duración
    #[ORM\Column(length: 255)]
    private ?string $duration = null; // Ejemplo: '3 weeks'

    // Nuevo campo: imagen
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null; // Puede almacenar la ruta de la imagen

    // Nuevo campo: estado de inscripción (abierto o cerrado)
    #[ORM\Column(type: 'boolean')]
    private bool $isRegistrationOpen = true; // Por defecto, la inscripción está abierta

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(string $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function isRegistrationOpen(): bool
    {
        return $this->isRegistrationOpen;
    }

    public function setRegistrationOpen(bool $isRegistrationOpen): static
    {
        $this->isRegistrationOpen = $isRegistrationOpen;

        return $this;
    }
}

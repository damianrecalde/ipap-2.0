<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: 'La fecha de inicio es obligatoria')]
    #[Assert\GreaterThanOrEqual("today", message: 'La fecha de inicio debe ser hoy o en el futuro.')]
    private ?\DateTimeInterface $start_date = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $end_date = null; // Nueva propiedad

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $close_date = null;

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'group')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Course $course = null;

    #[ORM\Column(type: 'string', length: 255)]
    private $format; // Valores: virtual, autogestionada, presencial

    #[ORM\Column(type: 'integer', nullable: true)]
    private int $actual_capacity = 0;

    #[ORM\Column(type: 'integer', options: ["default" => 160])]
    private int $max_capacity = 160;

    #[ORM\ManyToOne(targetEntity: City::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?City $city = null; 

    #[ORM\ManyToOne(targetEntity: Place::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Place $place = null; // Relación con Sede (lugar)

    #[ORM\Column(type: 'integer', nullable: true)]
    private int $reunion_number;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTimeInterface $start_date): self
    {
        $this->start_date = $start_date;

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

    public function getCloseDate(): ?\DateTimeInterface
    {
        return $this->close_date;
    }

    public function setCloseDate(?\DateTimeInterface $close_date): self
    {
        $this->close_date = $close_date;

        return $this;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): self
    {
        $this->course = $course;

        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function getActualCapacity(): ?int
    {
        return $this->actual_capacity;
    }

    public function setActualCapacity(int $actual_capacity): self
    {
        $this->actual_capacity = $actual_capacity;

        return $this;
    }

    public function getMaxCapacity(): ?int
    {
        return $this->max_capacity;
    }

    public function setMaxCapacity(int $max_capacity): self
    {
        $this->max_capacity = $max_capacity;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getReunionNumber(): ?int
    {
        return $this->reunion_number;
    }

    public function setReunionNumber(int $reunion_number): self
    {
        $this->reunion_number = $reunion_number;

        return $this;
    }

    public function __toString(): string
    {
        return $this->course->getName() . ' - ' . $this->start_date->format('d/m/Y');
    }

    public function getStartDateFormatted(): string
    {
        return $this->start_date ? $this->start_date->format('d/m/Y') : '';
    }

    public function getEndDateFormatted(): string
    {
        return $this->end_date ? $this->end_date->format('d/m/Y') : '';
    }

    public function getCloseDateFormatted(): string
    {
        return $this->close_date ? $this->close_date->format('d/m/Y') : '';
    }

    public function getCityName(): ?string
    {
        return $this->city ? $this->city->getName() : null;
    }

    public function getPlaceName(): ?string
    {
        return $this->place ? $this->place->getName() : null;
    }

    public function getCourseName(): ?string
    {
        return $this->course ? $this->course->getName() : null;
    }

}

<?php

namespace App\Entity;

use App\Repository\CursoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CursoRepository::class)]
class Curso
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $prefijo = null;

    #[ORM\Column]
    private ?bool $activo = null;

    #[ORM\Column(length: 255)]
    private ?string $modalidad_implementacion = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha_inicio = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fecha_fin = null;

    #[ORM\Column]
    private ?int $cantidad_horas = null;

    #[ORM\Column(length: 100)]
    private ?string $eje = null;

    #[ORM\Column]
    private ?bool $evaluacion = null;

    #[ORM\Column]
    private ?int $creditos = null;

    /**
     * @var Collection<int, Grupo>
     */
    #[ORM\OneToMany(targetEntity: Grupo::class, mappedBy: 'curso')]
    private Collection $grupos;

    public function __construct()
    {
        $this->grupos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getPrefijo(): ?string
    {
        return $this->prefijo;
    }

    public function setPrefijo(string $prefijo): static
    {
        $this->prefijo = $prefijo;

        return $this;
    }

    public function isActivo(): ?bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): static
    {
        $this->activo = $activo;

        return $this;
    }

    public function getModalidadImplementacion(): ?string
    {
        return $this->modalidad_implementacion;
    }

    public function setModalidadImplementacion(string $modalidad_implementacion): static
    {
        $this->modalidad_implementacion = $modalidad_implementacion;

        return $this;
    }

    public function getFechaInicio(): ?\DateTimeInterface
    {
        return $this->fecha_inicio;
    }

    public function setFechaInicio(\DateTimeInterface $fecha_inicio): static
    {
        $this->fecha_inicio = $fecha_inicio;

        return $this;
    }

    public function getFechaFin(): ?\DateTimeInterface
    {
        return $this->fecha_fin;
    }

    public function setFechaFin(?\DateTimeInterface $fecha_fin): static
    {
        $this->fecha_fin = $fecha_fin;

        return $this;
    }

    public function getCantidadHoras(): ?int
    {
        return $this->cantidad_horas;
    }

    public function setCantidadHoras(int $cantidad_horas): static
    {
        $this->cantidad_horas = $cantidad_horas;

        return $this;
    }

    public function getEje(): ?string
    {
        return $this->eje;
    }

    public function setEje(string $eje): static
    {
        $this->eje = $eje;

        return $this;
    }

    public function isEvaluacion(): ?bool
    {
        return $this->evaluacion;
    }

    public function setEvaluacion(bool $evaluacion): static
    {
        $this->evaluacion = $evaluacion;

        return $this;
    }

    public function getCreditos(): ?int
    {
        return $this->creditos;
    }

    public function setCreditos(int $creditos): static
    {
        $this->creditos = $creditos;

        return $this;
    }

    /**
     * @return Collection<int, Grupo>
     */
    public function getGrupos(): Collection
    {
        return $this->grupos;
    }

    public function addGrupo(Grupo $grupo): static
    {
        if (!$this->grupos->contains($grupo)) {
            $this->grupos->add($grupo);
            $grupo->setCurso($this);
        }

        return $this;
    }

    public function removeGrupo(Grupo $grupo): static
    {
        if ($this->grupos->removeElement($grupo)) {
            // set the owning side to null (unless already changed)
            if ($grupo->getCurso() === $this) {
                $grupo->setCurso(null);
            }
        }

        return $this;
    }
}

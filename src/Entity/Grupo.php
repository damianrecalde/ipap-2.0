<?php

namespace App\Entity;

use App\Repository\GrupoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GrupoRepository::class)]
class Grupo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha_inicio = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fecha_fin = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fecha_cierre = null;

    #[ORM\Column(length: 255)]
    private ?string $formato = null;

    #[ORM\Column(nullable: true)]
    private ?int $cupo_actual = null;

    #[ORM\Column]
    private ?int $cupo_maximo = null;

    #[ORM\Column(nullable: true)]
    private ?int $cantidad_encuentros = null;

    #[ORM\ManyToOne(inversedBy: 'grupos')]
    private ?City $localidad = null;

    #[ORM\ManyToOne(inversedBy: 'grupos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Curso $curso = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFechaCierre(): ?\DateTimeInterface
    {
        return $this->fecha_cierre;
    }

    public function setFechaCierre(?\DateTimeInterface $fecha_cierre): static
    {
        $this->fecha_cierre = $fecha_cierre;

        return $this;
    }

    public function getFormato(): ?string
    {
        return $this->formato;
    }

    public function setFormato(string $formato): static
    {
        $this->formato = $formato;

        return $this;
    }

    public function getCupoActual(): ?int
    {
        return $this->cupo_actual;
    }

    public function setCupoActual(?int $cupo_actual): static
    {
        $this->cupo_actual = $cupo_actual;

        return $this;
    }

    public function getCupoMaximo(): ?int
    {
        return $this->cupo_maximo;
    }

    public function setCupoMaximo(int $cupo_maximo): static
    {
        $this->cupo_maximo = $cupo_maximo;

        return $this;
    }

    public function getCantidadEncuentros(): ?int
    {
        return $this->cantidad_encuentros;
    }

    public function setCantidadEncuentros(?int $cantidad_encuentros): static
    {
        $this->cantidad_encuentros = $cantidad_encuentros;

        return $this;
    }

    public function getLocalidad(): ?City
    {
        return $this->localidad;
    }

    public function setLocalidad(?City $localidad): static
    {
        $this->localidad = $localidad;

        return $this;
    }

    public function getCurso(): ?Curso
    {
        return $this->curso;
    }

    public function setCurso(?Curso $curso): static
    {
        $this->curso = $curso;

        return $this;
    }
}

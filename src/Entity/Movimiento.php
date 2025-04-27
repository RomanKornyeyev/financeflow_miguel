<?php

namespace App\Entity;

use App\Repository\MovimientoRepository;
use App\Enum\MovimientoTipo;
use App\Enum\MovimientoCategoria;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MovimientoRepository::class)]
class Movimiento
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', enumType: MovimientoTipo::class)]
    private MovimientoTipo $tipoTransaccion;

    #[ORM\Column(type: 'string', enumType: MovimientoCategoria::class)]
    private MovimientoCategoria $categoria;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $importe;

    #[ORM\Column(type: 'string', length: 255)]
    private string $concepto;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $fechaMovimiento;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Usuario $usuario;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->fechaMovimiento = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTipoTransaccion(): MovimientoTipo
    {
        return $this->tipoTransaccion;
    }

    public function setTipoTransaccion(MovimientoTipo $tipoTransaccion): self
    {
        $this->tipoTransaccion = $tipoTransaccion;
        return $this;
    }

    public function getCategoria(): MovimientoCategoria
    {
        return $this->categoria;
    }

    public function setCategoria(MovimientoCategoria $categoria): self
    {
        $this->categoria = $categoria;
        return $this;
    }

    public function getImporte(): string
    {
        return $this->importe;
    }

    public function setImporte(string $importe): self
    {
        $this->importe = $importe;
        return $this;
    }

    public function getConcepto(): string
    {
        return $this->concepto;
    }

    public function setConcepto(string $concepto): self
    {
        $this->concepto = $concepto;
        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getFechaMovimiento(): \DateTimeInterface
    {
        return $this->fechaMovimiento;
    }

    public function setFechaMovimiento(\DateTimeInterface $fechaMovimiento): self
    {
        $this->fechaMovimiento = $fechaMovimiento;
        return $this;
    }

    public function getUsuario(): Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(Usuario $usuario): self
    {
        $this->usuario = $usuario;
        return $this;
    }
}

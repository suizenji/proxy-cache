<?php

namespace App\Entity;

use App\Repository\HttpContextRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HttpContextRepository::class)]
class HttpContext extends Http
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $tranId = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $f1 = null;

    #[ORM\Column(length: 255)]
    private ?string $f2 = null;

    #[ORM\Column(length: 255)]
    private ?string $f3 = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTranId(): ?string
    {
        return $this->tranId;
    }

    public function setTranId(string $tranId): self
    {
        $this->tranId = $tranId;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getF1(): ?string
    {
        return $this->f1;
    }

    public function setF1(string $f1): self
    {
        $this->f1 = $f1;

        return $this;
    }

    public function getF2(): ?string
    {
        return $this->f2;
    }

    public function setF2(string $f2): self
    {
        $this->f2 = $f2;

        return $this;
    }

    public function getF3(): ?string
    {
        return $this->f3;
    }

    public function setF3(string $f3): self
    {
        $this->f3 = $f3;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}

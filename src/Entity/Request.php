<?php

namespace App\Entity;

use App\Repository\RequestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RequestRepository::class)]
class Request
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $tranId = null;

    #[ORM\Column(length: 7)]
    private ?string $method = null;

    #[ORM\Column(length: 255)]
    private ?string $uri = null;

    #[ORM\Column]
    private ?float $version = null;

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

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function setUri(string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    public function getVersion(): ?float
    {
        return $this->version;
    }

    public function setVersion(float $version): self
    {
        $this->version = $version;

        return $this;
    }
}

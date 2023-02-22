<?php

namespace App\Entity;

use App\Repository\HttpBodyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HttpBodyRepository::class)]
class HttpBody extends Http
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $tranId = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: Types::BLOB)]
    private mixed $content = null;

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

    public function getContent(): mixed
    {
        return $this->content;
    }

    public function getContentStr(): string
    {
        if ('resource' === gettype($this->content)) {
            return stream_get_contents($this->content);
        }

        return $this->content;
    }

    public function setContent(mixed $content): self
    {
        $this->content = $content;

        return $this;
    }
}

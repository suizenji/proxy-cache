<?php

namespace App\Entity;

use App\Repository\CacheRuleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CacheRuleRepository::class)]
class CacheRule
{
    public const TYPE_SCHEME_HOST = 'scheme_host';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $cond = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCond(): ?string
    {
        return $this->cond;
    }

    public function setCond(string $cond): self
    {
        $this->cond = $cond;

        return $this;
    }
}

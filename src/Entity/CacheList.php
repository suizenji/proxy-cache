<?php

namespace App\Entity;

use App\Repository\CacheListRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CacheListRepository::class)]
class CacheList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $rule = null;

    #[ORM\Column(length: 255)]
    private ?string $cond = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRule(): ?string
    {
        return $this->rule;
    }

    public function setRule(string $rule): self
    {
        $this->rule = $rule;

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

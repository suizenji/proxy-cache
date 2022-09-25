<?php

namespace App\Entity;

use App\Repository\CacheRuleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CacheRuleRepository::class)]
class CacheRule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $judgeType = null;

    #[ORM\Column(length: 255)]
    private ?string $judgeCond = null;

    #[ORM\Column(length: 255)]
    private ?string $resType = null;

    #[ORM\Column(length: 255)]
    private ?string $resCond = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJudgeType(): ?string
    {
        return $this->judgeType;
    }

    public function setJudgeType(string $judgeType): self
    {
        $this->judgeType = $judgeType;

        return $this;
    }

    public function getJudgeCond(): ?string
    {
        return $this->judgeCond;
    }

    public function setJudgeCond(string $judgeCond): self
    {
        $this->judgeCond = $judgeCond;

        return $this;
    }

    public function getResType(): ?string
    {
        return $this->resType;
    }

    public function setResType(string $resType): self
    {
        $this->resType = $resType;

        return $this;
    }

    public function getResCond(): ?string
    {
        return $this->resCond;
    }

    public function setResCond(string $resCond): self
    {
        $this->resCond = $resCond;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Domain\Entity\Traits;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;

trait TimeStamps
{
    #[Column(type : Types::DATETIMETZ_MUTABLE, options : ['default' => 'CURRENT_TIMESTAMP'])]
    private ?DateTime $createdAt = null;

    #[Column(type : Types::DATETIMETZ_MUTABLE, nullable : true)]
    private ?DateTime $updatedAt = null;

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    #[PrePersist]
    public function prePersistCreatedAt(): void
    {
        $this->setCreatedAt(new DateTime());
    }

    #[PreUpdate]
    public function prePersistUpdatedAt(): void
    {
        $this->setUpdatedAt(new DateTime());
    }
}

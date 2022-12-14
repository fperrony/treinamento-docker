<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Domain\Entity\Interfaces;

use DateTime;

interface Entity
{
    public function getCreatedAt(): ?DateTime;

    public function setCreatedAt(DateTime $createdAt): static;

    public function getUpdatedAt(): ?DateTime;

    public function setUpdatedAt(DateTime $updatedAt): static;

    public function toArray(): array;
}

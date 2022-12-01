<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Domain\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use IXCSoft\TreinamentoDocker\Domain\Entity\Traits\TimeStamps;

#[Entity]
#[HasLifecycleCallbacks]
class Owner implements Interfaces\Entity
{
    use TimeStamps;

    #[Id]
    #[Column(type : Types::INTEGER)]
    #[GeneratedValue(strategy : 'AUTO')]
    private int $id;

    #[Column(type : Types::STRING)]
    private string $name;

    #[Column(type : Types::STRING)]
    private string $email;

    #[OneToMany(mappedBy : 'owner', targetEntity : Pet::class)]
    private Collection $pets;

    public function __construct()
    {
        $this->pets = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Owner
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): Owner
    {
        $this->email = $email;
        return $this;
    }

    public function getPets(): Collection
    {
        return $this->pets;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->getUpdatedAt() !== null ? $this->getUpdatedAt()->format('Y-m-d H:i:s Z') : '',
            'pets' => $this->getPets()->map(fn(Pet $pet) => $pet->getId())->toArray(),
        ];
    }
}

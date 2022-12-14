<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Domain\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use IXCSoft\TreinamentoDocker\Domain\Entity\Pet\Breed;
use IXCSoft\TreinamentoDocker\Domain\Entity\Traits\TimeStamps;

#[Entity]
#[HasLifecycleCallbacks]
class Pet implements Interfaces\Entity
{
    use TimeStamps;

    #[Id]
    #[Column(type : Types::INTEGER)]
    #[GeneratedValue(strategy : 'AUTO')]
    private int $id;

    #[Column(type : Types::STRING)]
    private string $name;

    #[Column(type : Types::INTEGER)]
    private int $breed;

    #[ManyToOne(targetEntity : Owner::class, inversedBy : 'pets')]
    #[JoinColumn(name : 'owner_id', referencedColumnName : 'id')]
    private Owner $owner;

    public function __construct()
    {
       // $this->setCreatedAt(new DateTime());
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Pet
    {
        $this->name = $name;
        return $this;
    }

    public function getBreed(): Breed
    {
        return Breed::from($this->breed);
    }

    public function setBreed(Breed $breed): Pet
    {
        $this->breed = $breed->value;
        return $this;
    }

    public function getOwner(): Owner
    {
        return $this->owner;
    }

    public function setOwner(Owner $owner): Pet
    {
        $this->owner = $owner;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'breed' => $this->getBreed()->value,
            'owner' => $this->getOwner()->getId(),
            'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->getUpdatedAt() !== null ? $this->getUpdatedAt()->format('Y-m-d H:i:s') : '',
        ];
    }
}

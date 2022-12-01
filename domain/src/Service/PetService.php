<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Domain\Service;

use Doctrine\ORM\EntityManager;
use IXCSoft\TreinamentoDocker\Domain\Entity\Pet;

final class PetService extends AppService
{
    public function __construct(EntityManager $entityManager)
    {
        $this->className = Pet::class;
        parent::__construct($entityManager);
    }

    public function populateEntity(array $data, ?int $id = null): Pet
    {
        $pet = new Pet();
        if ($id) {
            $pet = $this->getRepository()->find($id);
            if ($data['name'] !== null && $data['name'] !== $pet->getName()) {
                $pet->setName($data['name']);
            }
            if ($data['breed'] !== null && $data['breed'] !== $pet->getBreed()) {
                $pet->setBreed($data['breed'] instanceof Pet\Breed
                    ? $data['breed']
                    : Pet\Breed::from((int) $data['breed']));
            }
            if ($data['owner'] !== null && $data['owner'] !== $pet->getOwner()->getId()) {
                $ownerService = new OwnerService($this->entityManager);
                $owner = $ownerService->findOneById($data['owner']);
                $pet->setOwner($owner);
            }
            return $pet;
        }
        if ($data['name'] !== null) {
            $pet->setName($data['name']);
        }
        if ($data['breed'] !== null) {
            $pet->setBreed($data['breed'] instanceof Pet\Breed
                ? $data['breed']
                : Pet\Breed::tryFrom((int) $data['breed']));
        }
        if ($data['owner'] !== null) {
            $ownerService = new OwnerService($this->entityManager);
            $owner = $ownerService->findOneById($data['owner']);
            $pet->setOwner($owner);
        }
        return $pet;
    }
}

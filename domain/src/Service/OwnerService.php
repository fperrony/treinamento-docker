<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Domain\Service;

use Doctrine\ORM\EntityManager;
use IXCSoft\TreinamentoDocker\Domain\Entity\Owner;

final class OwnerService extends AppService
{
    public function __construct(EntityManager $entityManager)
    {
        $this->className = Owner::class;
        parent::__construct($entityManager);
    }

    public function populateEntity(array $data, ?int $id = null): Owner
    {
        $owner = new Owner();
        if ($id) {
            $owner = $this->getRepository()->find($id);
            if ($data['name'] !== null && $data['name'] !== $owner->getName()) {
                $owner->setName($data['name']);
            }
            if ($data['email'] !== null && $data['email'] !== $owner->getEmail()) {
                $owner->setEmail($data['email']);
            }
            return $owner;
        }
        if ($data['name'] !== null) {
            $owner->setName($data['name']);
        }
        if ($data['email'] !== null) {
            $owner->setEmail($data['email']);
        }
        return $owner;
    }
}

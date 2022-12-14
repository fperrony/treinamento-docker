<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Domain\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Iterator;
use IXCSoft\TreinamentoDocker\Domain\Entity\Interfaces\Entity;

abstract class AppService
{
    protected string $className;
    protected EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findOneById(int $id): ?Entity
    {
        return $this->getRepository()->findOneBy(['id' => $id]);
    }

    public function findAll(): Iterator
    {
        foreach ($this->getRepository()->findAll() as $entity) {
            yield $entity;
        }
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function store(Entity $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        $this->entityManager->refresh($entity);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function delete(Entity $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    public abstract function populateEntity(array $data, ?int $id = null): Entity;

    protected function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository($this->className);
    }
}

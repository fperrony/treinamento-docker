<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Domain\Tests\Integration;

use DateTime;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Iterator;
use IXCSoft\TreinamentoDocker\Domain\Entity\Interfaces\Entity;
use IXCSoft\TreinamentoDocker\Domain\Service\AppService;
use RedisException;

abstract class ServiceTestCase extends AppTestCase
{
    protected AppService $appService;

    /**
     * @dataProvider dataProvider
     */
    public function testPopulateNewEntity(int $id, array $data): void
    {
        $entity = $this->appService->populateEntity($data);
        $this->validateEntity($entity, $data, $id);
        $this->validateTimestamps($entity, false, false);
    }

    /**
     * @depends      testStore
     * @dataProvider dataProvider
     */
    public function testPopulateUpdateEntity(int $id, array $data): void
    {
        $entity = $this->appService->populateEntity($data, $id);
        $this->validateEntity($entity, $data, $id);
        $this->assertEquals($id, $entity->getId());
        $this->validateTimestamps($entity, true, false);
    }

    /**
     * @throws OptimisticLockException
     * @throws RedisException
     * @throws ORMException
     * @throws Exception
     * @throws RedisException
     * @dataProvider dataProvider
     */
    public function testStore(int $id, array $data): void
    {
        $entity = $this->appService->populateEntity($data);
        $this->appService->store($entity);
        $this->assertEquals($id, $entity->getId());
        $this->validateEntity($entity, $data, $id);
        $this->validateEntityToArray($entity, $data, $id);
        $this->validateTimestamps($entity, true, false);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @depends      testStore
     * @dataProvider dataProvider
     */
    public function testStoreUpdate(int $id, array $data): void
    {
        $changedData = $this->changeData($data);
        $entity = $this->appService->populateEntity($changedData, $id);
        $this->appService->store($entity);
        $entity = $this->appService->findOneById($id);
        $this->validateEntity($entity, $changedData, $id);
        $this->validateEntityToArray($entity, $changedData, $id);
        $this->validateOriginalData($data, $entity);
        $this->validateTimestamps($entity, true, true);
    }

    /**
     * @depends     testStoreUpdate
     */
    public function testFindAll(): void
    {
        foreach ($this->appService->findAll() as $entity) {
            $this->validateTimestamps($entity, true, true);
        }
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @depends      testFindAll
     * @dataProvider dataProvider
     */
    public function testDelete(int $id): void
    {
        $entity = $this->appService->findOneById($id);
        $this->appService->delete($entity);
        $entity = $this->appService->findOneById($id);
        $this->assertNull($entity);
    }

    public abstract function dataProvider(): Iterator;

    protected function validateEntityToArray(Entity $entity, array $data, int $id): void
    {
        $data['id'] = $id;
        $entityArray = $entity->toArray();
        $comparable = array_diff_ukey($data, $entityArray, fn($a, $b) => ($a === $b) ? 1 : -1);
        $this->assertEquals($data, $comparable);
    }

    protected abstract function validateEntity(Entity $entity, array $data, ?int $id = null): void;

    protected abstract function validateOriginalData(array $originalData, Entity $entity): void;

    protected abstract function changeData(array $data): array;

    private function validateTimestamps(Entity $entity, bool $created, bool $updated): void
    {
        $this->assertSame($created, $entity->getCreatedAt() instanceof DateTime);
        $this->assertSame($updated, $entity->getUpdatedAt() instanceof DateTime);
    }
}

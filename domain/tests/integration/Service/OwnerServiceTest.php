<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Domain\Tests\Integration\Service;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\ORMException;
use Iterator;
use IXCSoft\TreinamentoDocker\Domain\Entity\Interfaces\Entity;
use IXCSoft\TreinamentoDocker\Domain\EntityManagerFactory;
use IXCSoft\TreinamentoDocker\Domain\Service\OwnerService;
use IXCSoft\TreinamentoDocker\Domain\Tests\Integration\ServiceTestCase;
use RedisException;

class OwnerServiceTest extends ServiceTestCase
{
    /**
     * @throws RedisException
     * @throws ORMException
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->appService = new OwnerService(EntityManagerFactory::getInstance());
        parent::setUp();
    }

    public function dataProvider(): Iterator
    {
        yield [1, ['name' => 'João', 'email' => 'joao@teste.com']];
        yield [2, ['name' => 'Maria', 'email' => 'maria@teste.com']];
        yield [3, ['name' => 'José', 'email' => 'jose@teste.com']];
    }

    protected function validateEntity(Entity $entity, array $data, ?int $id = null): void
    {
        $this->assertEquals($data['name'], $entity->getName());
        $this->assertEquals($data['email'], $entity->getEmail());
        $this->assertCount(0, $entity->getPets());
    }

    protected function changeData(array $data): array
    {
        $data['name'] = $data['name'] . ' - Changed';
        $data['email'] = $data['email'] . '.br';
        return $data;
    }

    protected function validateOriginalData(array $originalData, Entity $entity): void
    {
        $this->assertNotEquals($originalData['name'], $entity->getName());
        $this->assertNotEquals($originalData['email'], $entity->getEmail());
    }
}

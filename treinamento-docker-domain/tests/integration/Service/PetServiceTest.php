<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Domain\Tests\Integration\Service;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\ORMException;
use Iterator;
use IXCSoft\TreinamentoDocker\Domain\Entity\Interfaces\Entity;
use IXCSoft\TreinamentoDocker\Domain\Entity\Owner;
use IXCSoft\TreinamentoDocker\Domain\Entity\Pet\Breed;
use IXCSoft\TreinamentoDocker\Domain\Service\PetService;
use IXCSoft\TreinamentoDocker\Domain\Tests\Integration\EntityManagerFactory;
use IXCSoft\TreinamentoDocker\Domain\Tests\Integration\ServiceTestCase;
use RedisException;

final class PetServiceTest extends ServiceTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $owner = new Owner();
        $owner->setName('Marcy')
            ->setEmail('marcy@teste.com.br');
        self::$entityManager->persist($owner);
        $owner = new Owner();
        $owner->setName('Cindy')
            ->setEmail('cindy@teste.com.br');
        self::$entityManager->persist($owner);
        self::$entityManager->flush();
    }

    /**
     * @throws RedisException
     * @throws ORMException
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->appService = new PetService(EntityManagerFactory::getInstance());
        parent::setUp();
    }

    public function dataProvider(): Iterator
    {
        yield [1, ['name' => 'Fido', 'breed' => Breed::dog, 'owner' => 1]];
        yield [2, ['name' => 'Garfield', 'breed' => Breed::cat, 'owner' => 2]];
        yield [3, ['name' => 'Nemo', 'breed' => Breed::fish, 'owner' => 1]];
    }

    protected function validateEntity(Entity $entity, array $data, ?int $id = null): void
    {
        $this->assertEquals($data['name'], $entity->getName());
        $this->assertEquals($data['breed'], $entity->getBreed());
        $this->assertEquals($data['owner'], $entity->getOwner()->getId());
    }

    protected function validateOriginalData(array $originalData, Entity $entity): void
    {
        $this->assertNotEquals($originalData['name'], $entity->getName());
    }

    protected function changeData(array $data): array
    {
        $data['name'] = $data['name'] . ' - Changed';
        $data['breed'] = Breed::dog;
        $data['owner'] = 2;
        return $data;
    }
}

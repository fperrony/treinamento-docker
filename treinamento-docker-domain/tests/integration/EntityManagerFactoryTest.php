<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Domain\Tests\Integration;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use PHPUnit\Framework\TestCase;
use RedisException;

class EntityManagerFactoryTest extends TestCase
{
    /**
     * @throws RedisException
     * @throws ORMException
     * @throws Exception
     */
    public function testGetInstance(): void
    {
        EntityManagerFactory::resetInstance();
        $this->assertInstanceOf(expected : EntityManager::class, actual : EntityManagerFactory::getInstance());
        $params = EntityManagerFactory::getInstance()->getConnection()->getParams();
        $this->assertEquals('primary', $params['primary']['host']);
        $this->assertEquals('replica1', $params['replica'][0]['host']);
        $this->assertEquals('replica2', $params['replica'][1]['host']);
    }

    /**
     * @throws RedisException
     * @throws ORMException
     * @throws Exception
     */
    public function testGetInstanceSingleConnection(): void
    {
        $this->assertInstanceOf(expected : EntityManager::class, actual : EntityManagerFactory::getInstanceSingleConnection());
        EntityManagerFactory::resetInstance();
        $this->assertInstanceOf(expected : EntityManager::class, actual : EntityManagerFactory::getInstanceSingleConnection());
        $params = EntityManagerFactory::getInstanceSingleConnection()->getConnection()->getParams();
        $this->assertEquals('primary', $params['host']);
        $this->assertFalse(isset($params['replica']));
    }
}

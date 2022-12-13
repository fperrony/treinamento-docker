<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Domain\Tests\Integration;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use IXCSoft\TreinamentoDocker\Domain\Tests\Integration\Traits\Schema;
use PHPUnit\Framework\TestCase;
use RedisException;

abstract class AppTestCase extends TestCase
{
    use Schema;

    protected static EntityManager $entityManager;

    /**
     * @throws Exception
     * @throws ORMException|RedisException|RedisException
     */
    public static function setUpBeforeClass(): void
    {
        self::dropSchema();
        self::createSchema();
        EntityManagerFactory::resetInstance();
        self::$entityManager = EntityManagerFactory::getInstance();
        usleep(100);
    }

    public static function tearDownAfterClass(): void
    {
        try {
            self::dropSchema();
        } catch (Exception|RedisException|ORMException $exception) {
            echo $exception->getMessage();
        }
    }
}

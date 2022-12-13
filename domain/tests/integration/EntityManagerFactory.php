<?php

declare(strict_types=1);


namespace IXCSoft\TreinamentoDocker\Domain\Tests\Integration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Connections\PrimaryReadReplicaConnection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\ORMSetup;
use Redis;
use RedisException;
use Symfony\Component\Cache\Adapter\RedisAdapter;

final class EntityManagerFactory
{
    private static ?EntityManager $entityManager = null;
    private static ?EntityManager $entityManagerSingleConnection = null;

    /**
     * @throws Exception
     * @throws ORMException
     * @throws RedisException
     */
    public static function getInstance(): EntityManager
    {
        if (!self::$entityManager instanceof EntityManager) {
            $config = ORMSetup::createAttributeMetadataConfiguration(
                paths     : array(__DIR__ . "/../src/Entity"),
                isDevMode : true,
            );
            self::configureCache($config);
            self::$entityManager = EntityManager::create(self::configureConnection(), $config);
        }
        return self::$entityManager;
    }

    /**
     * @throws RedisException
     * @throws Exception
     * @throws ORMException
     */
    public static function getInstanceSingleConnection(): EntityManager
    {
        if (!self::$entityManagerSingleConnection instanceof EntityManager) {
            $config = ORMSetup::createAttributeMetadataConfiguration(
                paths     : array(__DIR__ . "/../../src/Entity"),
                isDevMode : true,
            );
            self::configureCache($config);
            self::$entityManagerSingleConnection = EntityManager::create(self::configureUniqueConnection(), $config);
        }
        return self::$entityManagerSingleConnection;
    }

    public static function resetInstance(): void
    {
        if (self::$entityManager instanceof EntityManager) {
            self::$entityManager->getConnection()->close();
            self::$entityManager->close();
            self::$entityManager = null;
        }
        if (self::$entityManagerSingleConnection instanceof EntityManager) {
            self::$entityManagerSingleConnection->getConnection()->close();
            self::$entityManagerSingleConnection->close();
            self::$entityManagerSingleConnection = null;
        }
    }

    /**
     * @throws Exception
     */
    private static function configureConnection(): Connection
    {
        return DriverManager::getConnection([
            'wrapperClass' => PrimaryReadReplicaConnection::class,
            'driver' => 'pdo_pgsql',
            'primary' => ['user' => 'postgres', 'password' => 'password', 'host' => 'primary', 'dbname' => 'testdb'],
            'replica' => [
                ['user' => 'testuser', 'password' => 'password', 'host' => 'replica1', 'dbname' => 'testdb'],
                ['user' => 'testuser', 'password' => 'password', 'host' => 'replica2', 'dbname' => 'testdb'],
            ]
        ]);
    }

    /**
     * @throws Exception
     */
    private static function configureUniqueConnection(): Connection
    {
        return DriverManager::getConnection([
            'driver' => 'pdo_pgsql',
            'user' => 'postgres',
            'password' => 'password',
            'host' => 'primary',
            'dbname' => 'testdb',
        ]);
    }

    /**
     * @throws RedisException
     */
    private static function configureCache(Configuration $config): void
    {
        $redis = new Redis();
        $redis->connect('redis');
        $redis->flushDB();
        $config->setMetadataCache(new RedisAdapter($redis, 'metadata'));
        $config->setQueryCache(new RedisAdapter($redis, 'query'));
        $config->setResultCache(new RedisAdapter($redis, 'result'));
        $config->setHydrationCache(new RedisAdapter($redis, 'hydration'));
    }
}

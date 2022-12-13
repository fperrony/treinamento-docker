<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Domain\Tests\Integration\Traits;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use IXCSoft\TreinamentoDocker\Domain\Tests\Integration\EntityManagerFactory;
use RedisException;

trait Schema
{
    /**
     * @throws RedisException
     * @throws ORMException
     * @throws Exception
     */
    protected static function dropSchema(): void
    {
        $entityManager = EntityManagerFactory::getInstanceSingleConnection();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropSchema($entityManager->getMetadataFactory()->getAllMetadata());
    }

    /**
     * @throws RedisException
     * @throws ORMException
     * @throws Exception
     * @throws ToolsException
     */
    protected static function createSchema(): void
    {
        $entityManager = EntityManagerFactory::getInstanceSingleConnection();
        $entityManager->getConnection()->beginTransaction();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->createSchema($entityManager->getMetadataFactory()->getAllMetadata());
        $entityManager->getConnection()->executeQuery('GRANT SELECT ON ALL TABLES IN SCHEMA public TO testuser;');
        $entityManager->getConnection()->commit();
    }
}

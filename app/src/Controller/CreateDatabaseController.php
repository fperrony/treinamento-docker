<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Application\Controller;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use IXCSoft\TreinamentoDocker\Application\Fixtures\OwnerDataFixture;
use IXCSoft\TreinamentoDocker\Application\Fixtures\PetDataFixture;
use IXCSoft\TreinamentoDocker\Domain\Tests\Integration\EntityManagerFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class CreateDatabaseController
{
    private EntityManager $entityManager;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->entityManager = $container->get(EntityManager::class);
    }

    /**
     * @throws ORMException
     * @throws Exception
     * @throws ToolsException
     */
    public function createDatabaseAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->dropSchema();
        $this->createSchema();
        $response->getBody()->write("Database created!");
        return $response;
    }

    public function seedAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->seed();
        $response->getBody()->write("Database seeded!");
        return $response;
    }

    /**
     * @throws Exception
     */
    private function dropSchema(): void
    {
        $this->getConnection()->beginTransaction();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropSchema($this->entityManager->getMetadataFactory()->getAllMetadata());
        $this->getConnection()->commit();
    }

    /**
     * @throws ORMException
     * @throws Exception
     * @throws ToolsException
     */
    private function createSchema(): void
    {
        $this->entityManager->getConnection()->beginTransaction();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->createSchema($this->entityManager->getMetadataFactory()->getAllMetadata());
        if ($this->usetExists()) {
            $this->getConnection()->executeQuery('GRANT SELECT ON ALL TABLES IN SCHEMA public TO testuser;');
        }
        $this->getConnection()->commit();
    }

    /**
     * @throws Exception
     */
    private function usetExists(): bool
    {
        $sttmt = $this->getConnection()->prepare('SELECT 1 FROM pg_catalog.pg_user WHERE usename like \'testuser\';');
        $result = $sttmt->executeQuery();
        return $result->fetchOne() === 1;
    }

    private function getConnection(): Connection
    {
        return $this->entityManager->getConnection();
    }

    private function seed(): void
    {
        $loader = new Loader();
        $loader->addFixture(new OwnerDataFixture());
        $loader->addFixture(new PetDataFixture());
        $executor = new ORMExecutor($this->entityManager, new ORMPurger());
        $executor->execute($loader->getFixtures(), true);
    }
}

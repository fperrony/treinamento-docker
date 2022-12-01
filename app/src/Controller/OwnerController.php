<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Application\Controller;

use Doctrine\ORM\EntityManager;
use IXCSoft\TreinamentoDocker\Domain\Service\OwnerService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class OwnerController extends AppController
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->appService = new OwnerService($container->get(EntityManager::class));
    }
}

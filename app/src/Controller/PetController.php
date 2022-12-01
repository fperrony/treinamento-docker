<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Application\Controller;

use Doctrine\ORM\EntityManager;
use IXCSoft\TreinamentoDocker\Domain\Service\PetService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class PetController extends AppController
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->appService = new PetService($container->get(EntityManager::class));
    }
}

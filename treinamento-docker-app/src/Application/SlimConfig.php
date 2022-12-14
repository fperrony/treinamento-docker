<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Application\Application;

use Doctrine\ORM\Exception\ORMException;
use ErrorException;
use Pimple\Container;
use Pimple\Psr11\ServiceLocator;
use Psr\Cache\CacheException;

final class SlimConfig extends Config
{
    /**
     * @throws ORMException
     * @throws CacheException
     * @throws ErrorException
     */
    public function getContainer(): ServiceLocator
    {
        $doctrineConfig = new DoctrineConfig($this->configDir);
        $cacheConfig = new CacheConfig($this->configDir);
        $configs = array_merge(
            $this->getConfig(),
            $cacheConfig->getConfig(),
            $doctrineConfig->getConfig(),

        );
        $container = new Container($configs);
        return new ServiceLocator($container, $container->keys());
    }

    public function getConfig(): array
    {
        $defautl = [
            'slim' => [
                'displayErrorDetails' => false,
                'logErrors' => false,
                'logErrorDetails' => false,
            ],
        ];
        return array_merge($defautl, $this->parseConfigFile('slim.yml'));
    }
}

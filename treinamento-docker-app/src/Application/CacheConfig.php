<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Application\Application;

use ErrorException;
use IXCSoft\TreinamentoDocker\Application\Application\Traits\Cache;
use Memcached;
use Psr\Cache\CacheException;
use Redis;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

final class CacheConfig extends Config
{
    use Cache;

    /**
     * @throws CacheException
     * @throws ErrorException
     */
    public function getConfig(): array
    {
        $default = [
            'cache' => [
                'redis' => ['redis://127.0.0.1:6379'],
                'memcached' => ['memcached://127.0.0.1:11211']
            ],
        ];
        $cachesConfig = array_merge($default, $this->parseConfigFile('cache.yml'));
        return [
            Memcached::class => $this->memcachedAdapter($cachesConfig['cache']['memcached'], 'app'),
            Redis::class => $this->redisAdapter($cachesConfig['cache']['redis'], 'app'),
            FilesystemAdapter::class => new FilesystemAdapter('app'),
            ApcuAdapter::class => new ApcuAdapter('app')
        ];
    }
}

<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Application\Application\Traits;

use ErrorException;
use Psr\Cache\CacheException;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;

trait Cache
{
    /**
     * @throws CacheException
     * @throws ErrorException
     */
    private function memcachedAdapter(array $memcachedDsns, string $namespace): MemcachedAdapter
    {
        return new MemcachedAdapter(MemcachedAdapter::createConnection($memcachedDsns), $namespace);
    }

    private function redisAdapter(array $redisDsns, string $namespace): RedisAdapter
    {
        $dsns = '';
        $concat = '?';
        foreach ($redisDsns as $dsn) {
            $dsn = str_replace('redis://', '', $dsn);
            $dsns = 'redis:' . $concat . 'host[' . $dsn . ']';
            $concat = '&';
        }
        return new RedisAdapter(RedisAdapter::createConnection($dsns), $namespace);
    }
}

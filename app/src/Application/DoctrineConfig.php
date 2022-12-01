<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Application\Application;

use Doctrine\DBAL\Connections\PrimaryReadReplicaConnection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\ORMSetup;
use ErrorException;
use Exception;
use IXCSoft\TreinamentoDocker\Application\Application\Traits\Cache;
use Psr\Cache\CacheException;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

final class DoctrineConfig extends Config
{

    use Cache;

    /**
     * @throws ORMException
     * @throws Exception
     * @throws CacheException
     */
    public function getConfig(): array
    {
        $configArray = $this->loadConfig()['doctrine'];
        if ($configArray['dbal']['primaryReplica'] === true) {
            $configArray['dbal']['wrapperClass'] = PrimaryReadReplicaConnection::class;
        }
        $paths = [];
        $proxyDir = str_replace('APP_PATH', APP_PATH, $configArray['orm']['proxyDir']);
        if (!is_dir($proxyDir)) {
            mkdir($proxyDir, 0777, true);
        }
        foreach ($configArray['orm']['paths'] as $path) {
            $paths[] = str_replace('APP_PATH', APP_PATH, $path);
        }
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths     : $paths,
            isDevMode : $configArray['orm']['isDevMode'],
            proxyDir  : $proxyDir,
        );
        self::configureCache($config, $configArray['cache']);
        $connection = DriverManager::getConnection($configArray['dbal']);
        $entityManager = EntityManager::create($connection, $config);
        return [EntityManager::class => $entityManager];
    }

    private function loadConfig(): array
    {
        $default = [
            'doctrine' => [
                'orm' => [
                    'isDevMode' => false,
                    'paths' => [APP_PATH . "/vendor/ixcsfot/treinamento-docker-domain/src/Entity"],
                    'proxyDir' => APP_PATH . '/var/doctrine/proxy',
                ],
                'dbal' => [
                    'primaryReplica' => false,
                    'driver' => 'pdo_pgsql',
                    'user' => 'postgresql',
                    'password' => 'password',
                    'host' => '127.0.0.1',
                    'dbname' => 'testdb',
                    'application_name' => 'app'
                ],
                'cache' => [
                    'metadata' => null,
                    'query' => null,
                    'result' => null,
                    'hydration' => null,
                ],
            ],
        ];
        array_unique(array_merge_recursive($default,$this->parseConfigFile('doctrine.yml')), SORT_REGULAR);
        return array_merge($default, $this->parseConfigFile('doctrine.yml'));
    }

    /**
     * @throws CacheException
     * @throws ErrorException
     */
    private function configureCache(Configuration $config, array $cacheConfig): void
    {
        $config->setMetadataCache($this->getCacheDriver($cacheConfig['metadata'], 'dctrn_metadata'));
        $config->setQueryCache($this->getCacheDriver($cacheConfig['query'], 'dctrn_query'));
        $config->setResultCache($this->getCacheDriver($cacheConfig['result'], 'dctrn_result'));
        $config->setHydrationCache($this->getCacheDriver($cacheConfig['hydration'], 'dctrn_hydration'));
    }

    /**
     * @throws CacheException
     * @throws ErrorException
     */
    private function getCacheDriver(string $config, $namespace): CacheItemPoolInterface
    {
        if (str_starts_with($config, 'redis://')) {
            return $this->redisAdapter([$config], $namespace);
        }
        if (str_starts_with($config, 'memcached://')) {
            return $this->memcachedAdapter([$config], $namespace);
        }
        if ($config === 'apcu') {
            return new ApcuAdapter($namespace);
        }
        return new FilesystemAdapter($namespace);
    }

}

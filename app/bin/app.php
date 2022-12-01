#!/usr/bin/env php
<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use IXCSoft\TreinamentoDocker\Application\Application\SlimConfig;
use Psr\Cache\CacheException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

require __DIR__ . '/../vendor/autoload.php';

!defined('APP_PATH') && define('APP_PATH', realpath(__DIR__ . '/..'));

$configPath = new SplFileInfo(__DIR__ . '/../configs/');
try {
    $slimConfig = new SlimConfig($configPath);
    $container = $slimConfig->getContainer();
    $entityManager = $container->get(EntityManager::class);
    $connection = $entityManager->getConnection();
} catch (NotFoundExceptionInterface|ContainerExceptionInterface|CacheException|ErrorException|ORMException $exception) {
    echo $exception->getMessage();
    exit(1);
}
try {
    $connection->beginTransaction();
    ConsoleRunner::run(new SingleManagerProvider($entityManager));
    $connection->commit();
} catch (NotFoundExceptionInterface|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
    $connection->rollback();
}

<?php

declare(strict_types=1);

use Doctrine\ORM\Exception\ORMException;
use IXCSoft\TreinamentoDocker\Application\Application\Route;
use IXCSoft\TreinamentoDocker\Application\Application\SlimConfig;
use IXCSoft\TreinamentoDocker\Application\Middleware\Auth;
use Psr\Cache\CacheException;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

!defined('APP_PATH') && define('APP_PATH', realpath(__DIR__ . '/..'));

$configPath = new SplFileInfo(__DIR__ . '/../configs/');
try {
    $slimConfig = new SlimConfig($configPath);
    $container = $slimConfig->getContainer();
    $app = AppFactory::createFromContainer($container);
    unset($slimConfig);
    unset($container);
} catch (ORMException|RedisException|ErrorException|CacheException $exception) {
    echo json_encode([$exception->getMessage()]);
    exit(1);
}
$app->add(new Auth($configPath));
(new Route($app))->register();
$app->run();

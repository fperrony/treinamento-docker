<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Application\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;
use SplFileInfo;

final class Auth implements MiddlewareInterface
{

    public function __construct(SplFileInfo $configPath)
    {
        $defaultConfig = ['save_handler' => 'files', 'save_path' => '/var/lib/php/sessions'];
        $iniConfig = [];
        $sessionConfigFile = $configPath->getPathname() . '/session.ini';
        if (file_exists($sessionConfigFile)) {
            $iniConfig = parse_ini_file($sessionConfigFile);
        }
        $config = array_merge($iniConfig, $defaultConfig);
        ini_set('session.save_handler', $config['save_handler']);
        ini_set('session.save_path', $config['save_path']);
        session_id(md5('teste_auth_session'));
        session_start();
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        if (empty($route)) {
            throw new HttpNotFoundException($request, 'Route not found');
        }
        $routeName = $route->getName();
        $publicRoutesArray = array('unauthenticated', 'login', 'home');
        if (empty($_SESSION['user']) && (!in_array($routeName, $publicRoutesArray))) {
            $routeParser = $routeContext->getRouteParser();
            $url = $routeParser->urlFor('unauthenticated');
            $response = new Response();
            return $response->withHeader('Location', $url)->withStatus(302);
        }
        return $handler->handle($request);
    }
}

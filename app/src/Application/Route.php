<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Application\Application;

use IXCSoft\TreinamentoDocker\Application\Controller\AuthController;
use IXCSoft\TreinamentoDocker\Application\Controller\CreateDatabaseController;
use IXCSoft\TreinamentoDocker\Application\Controller\OwnerController;
use IXCSoft\TreinamentoDocker\Application\Controller\PetController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;

final class Route
{
    private App $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function register(): void
    {
        $this->configureRoutes();
        $this->homeRoute();
        $this->authRoutes();
        $this->restFullRoutes();
        $this->otherRoutes();
    }

    private function configureRoutes(): void
    {
        $this->app->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
            $response = $handler->handle($request);
            return $response->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
        });
        $this->app->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
            $response = $handler->handle($request);
            return $response->withHeader('Content-Type', 'application/json');
        });
        $this->app->addRoutingMiddleware();
    }

    private function homeRoute(): void
    {
        $app = $this->app;
        $this->app->get('/', function (ServerRequestInterface $request, ResponseInterface $response) use ($app)  {
            $routes = [];
            foreach ($app->getRouteCollector()->getRoutes() as $route) {
                $routes[$route->getName()] = $route->getPattern();
            }
            $response->getBody()->write(json_encode([
                    'Hello! Read the docs' => 'https://documenter.getpostman.com/view/22089416/2s8YmNR3mi',
                    'routes' => $routes
                ]
            ));
            return $response;
        })->setName('home');
    }

    private function authRoutes(): void
    {
        $this->app->get('/unauthenticated', AuthController::class . ':unauthenticatedAction')->setName('unauthenticated');
        $this->app->post('/login', AuthController::class . ':loginAction')->setName('login');
        $this->app->post('/logout', AuthController::class . ':logoutAction')->setName('logout');
    }

    private function otherRoutes(): void
    {
        $controllers = [
            'createDatabase' => CreateDatabaseController::class,
            'seed' => CreateDatabaseController::class,
        ];
        foreach ($controllers as $name => $controller) {
            $this->app->get($this->preparePattern($name), $controller . ':' . $name . 'Action')->setName($name);
        }
    }

    private function restFullRoutes(): void
    {
        $controllers = [
            'owner' => OwnerController::class,
            'pet' => PetController::class,
        ];
        foreach ($controllers as $name => $controller) {
            $pattern = $this->preparePattern($name);
            $this->app->get($pattern, $controller . ':getAllAction')->setName('getAll' . $name);
            $this->app->get($pattern . '/{id}', $controller . ':getOneAction')->setName('getOne' . $name);
            $this->app->post($pattern, $controller . ':createAction')->setName('create' . $name);
            $this->app->put($pattern . '/{id}', $controller . ':updateAction')->setName('update' . $name);
            $this->app->delete($pattern . '/{id}', $controller . ':deleteAction')->setName('delete' . $name);
        }
    }

    private function preparePattern(string $name): string
    {
        return '/' . $this->camelToDashed($name);
    }

    private function camelToDashed($className): string
    {
        return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1_', $className));
    }

}

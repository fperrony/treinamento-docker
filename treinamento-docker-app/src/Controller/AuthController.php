<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Application\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class AuthController
{
    public function unauthenticatedAction(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $response->getBody()->write(json_encode(['msg' => 'Unauthenticated']));
        return $response;
    }

    public function loginAction(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $_SESSION['user'] = 'loged';
        $response->getBody()->write(json_encode(['msg' => 'Login OK!']));
        return $response;
    }

    public function logoutAction(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        unset($_SESSION['user']);
        $response->getBody()->write(json_encode(['msg' => 'Logout OK!']));
        return $response;
    }
}

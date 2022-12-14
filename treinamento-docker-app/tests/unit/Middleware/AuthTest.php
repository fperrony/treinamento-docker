<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Application\Tests\Unit\Middleware;

use Iterator;
use IXCSoft\TreinamentoDocker\Application\Middleware\Auth;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\RouteInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Routing\RouteContext;
use Slim\Routing\RoutingResults;
use SplFileInfo;

final class AuthTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testAuth(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $calls = [
            [RouteContext::ROUTE, null],
            [RouteContext::ROUTE_PARSER, null],
            [RouteContext::ROUTING_RESULTS, null],
            [RouteContext::BASE_PATH, null],
        ];
        $routerParserInterface = $this->createMock(RouteParserInterface::class);
        $routerParserInterface->expects($this->once())->method('urlFor')->willReturn('url');
        $request->expects($this->exactly(4))->method('getAttribute')
            ->withConsecutive(...$calls)
            ->willReturnOnConsecutiveCalls(
                $this->createMock(RouteInterface::class),
                $routerParserInterface,
                $this->createMock(RoutingResults::class),
                __DIR__ . '/../../'
            );

        $handler = $this->createMock(RequestHandlerInterface::class);
        $auth = new Auth($this->fileMock());
        $return = $auth->process($request, $handler);
        $this->assertInstanceOf(ResponseInterface::class, $return);
        $this->assertEquals('url', $return->getHeader('Location')[0]);
    }

    /**
     * @runInSeparateProcess
     * @dataProvider routeData
     */
    public function testAuthPublic(string $routeName): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $calls = [
            [RouteContext::ROUTE, null],
            [RouteContext::ROUTE_PARSER, null],
            [RouteContext::ROUTING_RESULTS, null],
            [RouteContext::BASE_PATH, null],
        ];
        $route = $this->createMock(RouteInterface::class);
        $route->method('getName')->willReturn($routeName);
        $request->expects($this->exactly(4))->method('getAttribute')
            ->withConsecutive(...$calls)
            ->willReturnOnConsecutiveCalls(
                $route,
                $this->createMock(RouteParserInterface::class),
                $this->createMock(RoutingResults::class),
                __DIR__ . '/../../'
            );

        $handler = $this->createMock(RequestHandlerInterface::class);
        $auth = new Auth($this->fileMock());
        $return = $auth->process($request, $handler);
        $this->assertInstanceOf(ResponseInterface::class, $return);
    }

    /**
     * @runInSeparateProcess
     */
    public function testRouteNull(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $calls = [
            [RouteContext::ROUTE, null],
            [RouteContext::ROUTE_PARSER, null],
            [RouteContext::ROUTING_RESULTS, null],
            [RouteContext::BASE_PATH, null],
        ];
        $request->expects($this->exactly(4))->method('getAttribute')
            ->withConsecutive(...$calls)
            ->willReturnOnConsecutiveCalls(
                null,
                $this->createMock(RouteParserInterface::class),
                $this->createMock(RoutingResults::class),
                __DIR__ . '/../../'
            );

        $handler = $this->createMock(RequestHandlerInterface::class);
        $auth = new Auth($this->fileMock());
        $this->expectException(HttpNotFoundException::class);
        ($auth->process($request, $handler));
    }

    public function routeData(): Iterator
    {
        yield ['login'];
        yield ['logout'];
        yield ['unauthenticated'];
    }

    private function fileMock(): SplFileInfo
    {
        $root = vfsStream::setup(md5(__CLASS__));
        $configFile = vfsStream::newFile('session.ini')->at($root);
        $content = 'save_handler="files"' . PHP_EOL;
        $content .= 'save_path="' . $root->url() . '"' . PHP_EOL;
        $configFile->setContent($content);
        return new SplFileInfo($root->url());
    }
}

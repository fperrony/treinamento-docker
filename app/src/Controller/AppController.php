<?php

declare(strict_types=1);

namespace IXCSoft\TreinamentoDocker\Application\Controller;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use InvalidArgumentException;
use IXCSoft\TreinamentoDocker\Domain\Service\AppService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TypeError;

abstract class AppController
{
    protected AppService $appService;

    public function getAllAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $returnData = [];
        foreach ($this->appService->findAll() as $entity) {
            $returnData[] = $entity->toArray();
        }
        return $this->prepareResponse($response, $returnData);
    }

    public function getOneAction(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $returnData = ['Not found'];
        $entity = $this->appService->findOneById((int) $args['id']);
        if ($entity !== null) {
            $returnData = $entity->toArray();
        }
        return $this->prepareResponse($response, $returnData);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function createAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->prepareResponse($response, $this->store($request));
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function updateAction(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $entity = $this->appService->findOneById($id);
        if ($entity === null) {
            return $this->prepareResponse($response, ['Not found'], 404);
        }
        return $this->prepareResponse($response, $this->store($request, $id));
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function deleteAction(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $returnData = ['msg' => 'Not found'];
        $entity = $this->appService->findOneById((int) $args['id']);
        if ($entity !== null) {
            $this->appService->delete($entity);
            $returnData = ['msg' => get_class($entity) . sprintf(' %s ', $args['id']) . 'deleted'];
        }
        return $this->prepareResponse($response, $returnData);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    private function store(ServerRequestInterface $request, ?int $id = null): array
    {
        $content = json_decode($request->getBody()->getContents(), true);
        $returnData = ['msg' => 'Invalid content', 'content' => $content];
        if (is_array($content)) {
            try {
                $entity = $this->appService->populateEntity($content, $id);
                $this->appService->store($entity);
                $returnData = ['msg' => get_class($entity) . ' created', 'entity' => $entity->toArray()];
            } catch (InvalidArgumentException|TypeError $exception) {
                $returnData = ['msg' => $exception->getMessage()];
            }
        }
        return $returnData;
    }

    private function prepareResponse(ResponseInterface $response, array $content, int $httpCode = 200): ResponseInterface
    {
        $response->getBody()->write(json_encode($content));
        $response->withStatus($httpCode);
        return $response;
    }
}

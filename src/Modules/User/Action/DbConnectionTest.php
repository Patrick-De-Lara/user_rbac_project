<?php

declare(strict_types=1);

namespace Modules\User\Action;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Db\Connection\ConnectionInterface;

final class DbConnectionTest
{
    public function __construct(
        private ConnectionInterface $db,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function __invoke(): ResponseInterface
    {
        $result = $this->db
            ->createCommand('SELECT 1')
            ->queryScalar();

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write("MySQL connection successful! Result: {$result}");

        return $response;
    }
}
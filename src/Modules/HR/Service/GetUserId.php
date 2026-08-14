<?php

declare(strict_types=1);

namespace Modules\HR\Service;


use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Session\SessionInterface;

final class GetUserId
{
    private SessionInterface $session;
    private ConnectionInterface $db;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(
        ConnectionInterface $db,
        ResponseFactoryInterface $responseFactory,
        SessionInterface $session
    ) {
        $this->session = $session;
        $this->db = $db;
        $this->responseFactory = $responseFactory;
        $id = $this->session->get('user_id');
    }


    public function getUserId(int $userId): ?int
    {
        $user = $this->db->createCommand('SELECT * FROM sys_user WHERE id = :id')
            ->bindValue(':id', $userId)
            ->queryOne();

        return $user['id'] ?? null;
    }
}
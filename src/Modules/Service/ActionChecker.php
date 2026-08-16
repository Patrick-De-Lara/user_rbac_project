<?php

declare(strict_types=1);

namespace Modules\Service;

use Yiisoft\Session\SessionInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Db\Connection\ConnectionInterface;


final class ActionChecker
{
    private SessionInterface $session;
    private UrlGeneratorInterface $urlGenerator;
    private ResponseFactoryInterface $responseFactory;
    private ConnectionInterface $db;

    public function __construct(
        SessionInterface $session, 
        UrlGeneratorInterface $urlGenerator, 
        ResponseFactoryInterface $responseFactory,
        ConnectionInterface $db
    )
    {
        $this->session = $session;
        $this->urlGenerator = $urlGenerator;
        $this->responseFactory = $responseFactory;
        $this->db = $db;
    }

    public function UserModuleChecker(string $moduleName, string $userId): array
    {
        $approve = $this->db->createCommand('
        SELECT 
            uhr.user_id,
            uhr.role_id,
            urha.action_id,
            um.code AS module_code,
            ua.code AS action_code
        FROM sys_user_has_role uhr
        INNER JOIN sys_user_role_has_action urha
            ON uhr.role_id = urha.role_id
        INNER JOIN sys_user_action ua
            ON ua.id = urha.action_id
        INNER JOIN sys_user_module um
            ON ua.module_id = um.id

        WHERE um.code LIKE :module AND uhr.user_id = :user ;
        ')->bindValue(':module', '%' . $moduleName . '%')
        ->bindValue(':user', $userId)
        ->queryAll();

        return $approve;
    }
}
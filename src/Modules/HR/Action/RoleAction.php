<?php

declare(strict_types=1);

namespace Modules\HR\Action;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Db\Connection\ConnectionInterface;
use Modules\HR\Service\SessionChecker;

final class RoleAction
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private UrlGeneratorInterface $urlGenerator,
        private WebViewRenderer $viewRenderer,
        private SessionInterface $session,
        private ConnectionInterface $db,
        private SessionChecker $sessionCheck
    ) {
    }

    public function render(): ResponseInterface
    {
        if($this->sessionCheck->isUserLoggedIn() === false) {
            return $this->sessionCheck->returnLogin();
        }

        $id = $this->session->get('user_id');

        $roles = $this->db->createCommand
        ('SELECT * FROM sys_user_has_role WHERE user_id = :user_id')
            ->bindValue(':user_id', $id)
            ->queryAll();

        return $this->viewRenderer
        ->withLayout('@src/Web/Shared/Layout/Dashboard/sidebar.php')
        ->render(
            __DIR__ . '/../View/RoleEdit.php',
            [
                'roles' => $roles ?? [],
            ]
        );
    }

}

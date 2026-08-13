<?php

declare(strict_types=1);

namespace Modules\User\Action;

use Yiisoft\Yii\View\Renderer\WebViewRenderer;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Yiisoft\Db\Connection\ConnectionInterface;


final class HomeAction
{
    public function __construct(
        private WebViewRenderer $viewRenderer,
        private UrlGeneratorInterface $urlGenerator,
        private SessionInterface $session,
        private ResponseFactoryInterface $responseFactory,
        private ConnectionInterface $db,
    )
    {
    }

    public function __invoke(): ResponseInterface
    {
        if(!$this->session->get('user_id')) {
            $url = $this->urlGenerator->generate('login');
            return $this->responseFactory
                ->createResponse(302)
                ->withHeader('Location', $url);
        }

        $user = $this->db
            ->createCommand('SELECT * FROM sys_user WHERE id = :id')
            ->bindValue(':id', $this->session->get('user_id'))
            ->queryOne();

        return $this->viewRenderer
        ->withLayout('@src/Web/Shared/Layout/Dashboard/sidebar.php')
        ->render(__DIR__ . '/../View/homepage.php',
            [
                'user' => $user,
            ],
        );
    }
}
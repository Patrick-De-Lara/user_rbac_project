<?php

declare(strict_types=1);

namespace Modules\User\Action;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Db\Connection\ConnectionInterface;

final class LoginAction
{
    public function __construct(
        private WebViewRenderer $viewRenderer,
        private UrlGeneratorInterface $urlGenerator,
        private ResponseFactoryInterface $responseFactory,
        private SessionInterface $session,
        private ConnectionInterface $db,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
    ): ResponseInterface {
        $data = $request->getParsedBody();

        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';
        

        // Check required fields
        if ($username === '' || $password === '') {
            return $this->viewRenderer
                ->withLayout('@src/Web/Shared/Layout/Auth/layout.php')
                ->render(
                    __DIR__ . '/../View/LoginPage.php',
                    [
                        'error' => 'Username and password are required',
                    ],
                );
        }


        $user = $this->db
            ->createCommand(
                'SELECT * FROM sys_user WHERE username = :username'
            )
            ->bindValue(':username', $username)
            ->queryOne();

        if ($user !== null && password_verify($password, $user['password'])) {
            $url = $this->urlGenerator->generate('home');

            $this->session->set('user_id', (string) $user['id']);

            return $this->responseFactory
                ->createResponse(302)
                ->withHeader('Location', $url);
        }

        //check if the account is active

        if($user !== null && $user['is_active'] === 0) {
            return $this->viewRenderer
                ->withLayout('@src/Web/Shared/Layout/Auth/layout.php')
                ->render(
                    __DIR__ . '/../View/LoginPage.php',
                    [
                        'error' => 'Your account is inactive. Please contact the administrator.',
                    ],
                );
        }


        // Temporary authentication
        // if ($username === 'admin' && $password === 'admin') {
        //     $url = $this->urlGenerator->generate('home');
        //     $this->session->set('user_id', '1');

        //     return $this->responseFactory
        //         ->createResponse(302)
        //         ->withHeader('Location', $url);
        // }

        // Authentication failed
        return $this->viewRenderer
            ->withLayout('@src/Web/Shared/Layout/Auth/layout.php')
            ->render(
                __DIR__ . '/../View/LoginPage.php',
                [
                    'error' => 'Invalid username or password',
                ],
            );
    }
}
<?php

namespace Modules\User\Action;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;
use Yiisoft\Session\SessionInterface;


final class LogoutAction
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private ResponseFactoryInterface $responseFactory,
        private SessionInterface $session,
    ) {
    }

    public function __invoke(): ResponseInterface
    {
       
        $this->session->remove('user_id');

        // Redirect to the login page
        $url = $this->urlGenerator->generate('login');
        
        return $this->responseFactory
            ->createResponse(302)
            ->withHeader('Location', $url);
    }
}
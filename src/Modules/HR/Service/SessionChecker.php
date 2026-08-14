<?php

declare(strict_types=1);

namespace Modules\HR\Service;

use Yiisoft\Session\SessionInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;


final class SessionChecker
{
    private SessionInterface $session;
    private UrlGeneratorInterface $urlGenerator;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(
        SessionInterface $session, 
        UrlGeneratorInterface $urlGenerator, 
        ResponseFactoryInterface $responseFactory)
    {
        $this->session = $session;
        $this->urlGenerator = $urlGenerator;
        $this->responseFactory = $responseFactory;
    }

    public function isUserLoggedIn(): bool
    {
        return $this->session->has('user_id');
    }

    public function returnLogin(): ResponseInterface
    {
        return $this->responseFactory
        ->createResponse(302)
        ->withHeader('Location', $this->urlGenerator->generate('login'));
    }
}
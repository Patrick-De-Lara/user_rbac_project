<?php

declare(strict_types=1);

namespace Modules\User\Action;

use Yiisoft\Yii\View\Renderer\WebViewRenderer;
use Psr\Http\Message\ResponseInterface;

final class LoginActionPage
{
    public function __construct(
        private WebViewRenderer $viewRenderer,
    )
    {
    }

    public function __invoke(): ResponseInterface
    {
        return $this->viewRenderer
        ->withLayout('@src/web/Shared/Layout/Auth/layout.php')
        ->render(__DIR__ . '/../View/LoginPage.php');
    }
}
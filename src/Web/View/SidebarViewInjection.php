<?php

declare(strict_types=1);

namespace App\Web\View;

use Modules\Service\ActionChecker;
use Modules\Service\GetUserId;
use Yiisoft\Yii\View\Renderer\CommonParametersInjectionInterface;

/**
 * Makes sidebar services available to both page templates and layouts.
 */
final class SidebarViewInjection implements CommonParametersInjectionInterface
{
    public function __construct(
        private readonly ActionChecker $actionChecker,
        private readonly GetUserId $getUserId,
    ) {}

    public function getCommonParameters(): array
    {
        return [
            'actionChecker' => $this->actionChecker,
            'getUserId' => $this->getUserId,
        ];
    }
}

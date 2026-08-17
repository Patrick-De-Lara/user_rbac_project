<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;

/**
 * @psalm-var list<callable(ContainerInterface): void>
 */
return [
    Static function(){
        date_default_timezone_set('Asia/Manila');
    }
];

<?php

declare(strict_types=1);

use App\Web;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;

return [
    Group::create()
        ->routes(
            Route::get('/default')
                ->action(Web\HomePage\Action::class)
                ->name('default'),
            Route::get('/login')
                ->action(Modules\User\Action\LoginActionPage::class)
                ->name('login'),
            Route::post('/login')
                ->action(Modules\User\Action\LoginAction::class)
                ->name('login.submit'),
            Route::get('/logout')
                ->action(Modules\User\Action\LogoutAction::class)
                ->name('logout'),
            Route::get('/home')
                ->action(Modules\User\Action\HomeAction::class)
                ->name('home'),
            Route::get('/db-test')
                ->action(Modules\User\Action\DbConnectionTest::class)
                ->name('db-test')
        ),
];

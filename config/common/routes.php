<?php

declare(strict_types=1);

use App\Web;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Modules\HR\Action\CreateUserAction;
use Modules\HR\Action\RoleAction;

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
                ->name('db-test'),

            
            //User Route
            Route::get('/create-user')
                ->action([CreateUserAction::class, 'render'])
                ->name('create-user'),
            Route::post('/create-user')
                ->action([CreateUserAction::class, 'create'])
                ->name('create-user.submit'),
            Route::get('/update-user/{id:\d+}')
                ->action([CreateUserAction::class, 'update'])
                ->name('update-user'),
            Route::post('/update-user/{id:\d+}')
                ->action([CreateUserAction::class, 'updateSubmit'])
                ->name('update-user.submit'),
            Route::get('/user-list')
                ->action([CreateUserAction::class, 'view'])
                ->name('user-list'),

            //Role Route
            Route::get('/role-list')
                ->action([RoleAction::class, 'render'])
                ->name('role-list'),    
        ),
];

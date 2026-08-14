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

            // -----------------------------------------------
            // User Routes
            // -----------------------------------------------
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

            // -----------------------------------------------
            // Role Routes
            // -----------------------------------------------

            // Main role management page (list + user role assignment UI)
            Route::get('/role-list')
                ->action([RoleAction::class, 'render'])
                ->name('role-list'),

            // AJAX — fetch assigned roles for a selected user
            Route::get('/role-list/user-roles')
                ->action([RoleAction::class, 'getUserRoles'])
                ->name('role-list.user-roles'),

            // Save role assignments for a user (called by JS fetch)
            Route::post('/role-list/save')
                ->action([RoleAction::class, 'save'])
                ->name('role-list.save'),

            // Create new role
            Route::get('/role-list/create')
                ->action([RoleAction::class, 'create'])
                ->name('role-list.create'),
            Route::post('/role-list/create')
                ->action([RoleAction::class, 'create'])
                ->name('role-list.create.submit'),

            // Edit existing role
            Route::get('/role-list/edit')
                ->action([RoleAction::class, 'edit'])
                ->name('role-list.edit'),
            Route::post('/role-list/edit')
                ->action([RoleAction::class, 'edit'])
                ->name('role-list.edit.submit'),

            // Delete role
            Route::post('/role-list/delete')
                ->action([RoleAction::class, 'delete'])
                ->name('role-list.delete'),
        ),
];
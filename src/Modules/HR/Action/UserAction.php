<?php

declare(strict_types=1);

namespace Modules\HR\Action;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Validator\Validator;
use Modules\HR\Model\SysUserDataValidation;
use Yiisoft\Router\HydratorAttribute\RouteArgument;
use Modules\Service\SessionChecker;
use Modules\Service\ActionChecker;
use Modules\HR\Trait\ActionHelper;

final class UserAction
{
    use ActionHelper;

    public function __construct(
        private WebViewRenderer $viewRenderer,
        private UrlGeneratorInterface $urlGenerator,
        private ResponseFactoryInterface $responseFactory,
        private SessionInterface $session,
        private ConnectionInterface $db,
        private Validator $validator,
        private SessionChecker $sessionCheck,
        private ActionChecker $actionChecker,
    ) {}

    // =========================================================
    // SHOW CREATE FORM
    // GET /create-user
    // =========================================================

    public function render(): ResponseInterface
    {
        if ($this->sessionCheck->isUserLoggedIn() === false) {
            return $this->sessionCheck->returnLogin();
        }

        return $this->viewRenderer
            ->withLayout('@src/Web/Shared/Layout/Dashboard/sidebar.php')
            ->render(__DIR__ . '/../View/UserCreationForm.php', [
                'isUpdate' => false,
                'flash'    => $this->pullFlash(),
            ]);
    }

    // =========================================================
    // HANDLE CREATE SUBMIT
    // POST /create-user
    // =========================================================

    public function create(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->sessionCheck->isUserLoggedIn() === false) {
            return $this->sessionCheck->returnLogin();
        }

        $data = $this->body($request);

        if ($data === []) {
            return $this->viewRenderer
                ->withLayout('@src/Web/Shared/Layout/Dashboard/sidebar.php')
                ->render(__DIR__ . '/../View/UserCreationForm.php', [
                    'isUpdate' => false,
                    'flash'    => ['error' => 'No data received.'],
                ]);
        }

        $formData = new SysUserDataValidation(
            firstName:       $data['firstname']       ?? null,
            lastName:        $data['lastname']        ?? null,
            middleName:      $data['middlename']      ?? null,
            birthday:        $data['birthday']        ?? null,
            sex:             $data['sex']             ?? null,
            birthPlace:      $data['birthplace']      ?? null,
            username:        $data['username']        ?? null,
            password:        $data['password']        ?? null,
            passwordConfirm: $data['password_confirm'] ?? null,
        );

        $result = $this->validator->validate($formData);

        if (!$result->isValid()) {
            return $this->viewRenderer
                ->withLayout('@src/Web/Shared/Layout/Dashboard/sidebar.php')
                ->render(__DIR__ . '/../View/UserCreationForm.php', [
                    'isUpdate' => false,
                    'errors'   => $result->getErrors(),
                    'formData' => $formData,
                    'flash'    => $this->pullFlash(),
                ]);
        }

        if ($formData->password !== $formData->passwordConfirm) {
            return $this->viewRenderer
                ->withLayout('@src/Web/Shared/Layout/Dashboard/sidebar.php')
                ->render(__DIR__ . '/../View/UserCreationForm.php', [
                    'isUpdate' => false,
                    'formData' => $formData,
                    'flash'    => ['error' => 'Passwords do not match.'],
                ]);
        }

        $this->db->createCommand()->insert('er_person', [
            'firstName'  => $formData->firstName,
            'lastName'   => $formData->lastName,
            'middleName' => $formData->middleName,
            'birthday'   => $formData->birthday,
            'birthPlace' => $formData->birthPlace,
            'sex'        => $formData->sex,
        ])->execute();

        $personId = $this->db->getLastInsertID();

        $this->db->createCommand()->insert('sys_user', [
            'username'     => $formData->username,
            'password'     => password_hash($formData->password, PASSWORD_DEFAULT),
            'person_id'    => $personId,
            'date_updated' => date('Y-m-d H:i:s'),
            'is_active'    => 1,
        ])->execute();

        $this->flash('success', 'User created successfully.');
        return $this->redirect('/user-list');
    }

    // =========================================================
    // SHOW EDIT FORM
    // GET /update-user/{id}
    // =========================================================

    public function update(#[RouteArgument] string $id): ResponseInterface
    {
        if ($this->sessionCheck->isUserLoggedIn() === false) {
            return $this->sessionCheck->returnLogin();
        }

        $user = $this->db->createCommand('
            SELECT
                u.id,
                u.username,
                p.firstName,
                p.lastName,
                p.middleName,
                p.birthday,
                p.sex,
                p.birthPlace
            FROM sys_user u
            INNER JOIN er_person p ON p.id = u.person_id
            WHERE u.id = :id
        ')
            ->bindValue(':id', $id)
            ->queryOne();

        if (!$user) {
            $this->flash('error', 'User not found.');
            return $this->redirect('/user-list');
        }

        return $this->viewRenderer
            ->withLayout('@src/Web/Shared/Layout/Dashboard/sidebar.php')
            ->render(__DIR__ . '/../View/UserCreationForm.php', [
                'formData' => $user,
                'userId'   => $id,
                'isUpdate' => true,
                'flash'    => $this->pullFlash(),
            ]);
    }

    // =========================================================
    // HANDLE EDIT SUBMIT
    // POST /update-user/{id}
    // =========================================================

    public function updateSubmit(ServerRequestInterface $request, #[RouteArgument] string $id): ResponseInterface
    {
        if ($this->sessionCheck->isUserLoggedIn() === false) {
            return $this->sessionCheck->returnLogin();
        }

        $data = $this->body($request);

        if ($data === []) {
            $this->flash('error', 'No data received.');
            return $this->redirect('/update-user/' . $id);
        }

        $user = $this->db->createCommand('SELECT * FROM sys_user WHERE id = :id')
            ->bindValue(':id', $id)
            ->queryOne();

        if (!$user) {
            $this->flash('error', 'User not found.');
            return $this->redirect('/user-list');
        }

        $this->db->createCommand()->update('er_person', [
            'firstName'  => $data['firstname']  ?? null,
            'lastName'   => $data['lastname']   ?? null,
            'middleName' => $data['middlename'] ?? null,
            'birthday'   => $data['birthday']   ?? null,
            'birthPlace' => $data['birthplace'] ?? null,
            'sex'        => $data['sex']        ?? null,
        ], ['id' => $user['person_id']])->execute();

        $userUpdate = [
            'date_updated' => date('Y-m-d H:i:s'),
            'username'     => $data['username'],
        ];

        if (!empty($data['password'])) {
            $userUpdate['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $this->db->createCommand()->update('sys_user', $userUpdate, ['id' => $id])->execute();

        $this->flash('success', 'User updated successfully.');
        return $this->redirect('/update-user/' . $id);
    }

    // =========================================================
    // USER LIST
    // GET /user-list
    // =========================================================

    public function view(): ResponseInterface
    {
        if ($this->sessionCheck->isUserLoggedIn() === false) {
            return $this->sessionCheck->returnLogin();
        }

        $id = $this->session->get('user_id');
        $viewItself = $this->actionChecker->UserModuleChecker('user', $id);

        $baseQuery = '
            SELECT
                u.id,
                u.username,
                u.is_active,
                u.date_updated,
                p.firstName,
                p.lastName,
                p.middleName
            FROM sys_user u
            LEFT JOIN er_person p ON p.id = u.person_id
        ';

        $canOnlyViewSelf = !empty($viewItself) && !in_array('all_view_employee_user',array_column($viewItself, 'action_code'));

        if ($canOnlyViewSelf) {
            $users = $this->db->createCommand($baseQuery . 'WHERE u.id = :id')
                ->bindValue(':id', $id)
                ->queryAll();
        } else {
            $users = $this->db->createCommand($baseQuery . 'ORDER BY p.lastName ASC, p.firstName ASC')
                ->queryAll();
        }

        return $this->viewRenderer
            ->withLayout('@src/Web/Shared/Layout/Dashboard/sidebar.php')
            ->render(__DIR__ . '/../View/UserList.php', [
                'users' => $users,
                'flash' => $this->pullFlash(),
            ]);
    }
}
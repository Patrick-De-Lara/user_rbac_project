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
use Modules\HR\Service\SessionChecker;

final class CreateUserAction
{
    
    public function __construct(
        private WebViewRenderer $viewRenderer,
        private UrlGeneratorInterface $urlGenerator,
        private ResponseFactoryInterface $responseFactory,
        private SessionInterface $session,
        private ConnectionInterface $db,
        private Validator $validator,
        private SessionChecker $sessionCheck
    ) {
    }

    // show creation user
    public function render(): ResponseInterface
    {
        if($this->sessionCheck->isUserLoggedIn() === false) {
            return $this->sessionCheck->returnLogin();
        }

        return $this->viewRenderer
            ->withLayout('@src/Web/Shared/Layout/Dashboard/sidebar.php')
            ->render(
                __DIR__ . '/../View/UserCreationForm.php',
                [
                    'isUpdate' => false,
                ],
            );
    }

    //  create user method
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();

        if($this->sessionCheck->isUserLoggedIn() === false) {
            return $this->sessionCheck->returnLogin();
        }

        if(!$data) {
            return $this->viewRenderer
                ->withLayout('@src/Web/Shared/Layout/Dashboard/sidebar.php')
                ->render(
                    __DIR__ . '/../View/UserCreationForm.php',
                    [
                        'error' => 'No data received',
                    ],
                );
        }

        $formData = new SysUserDataValidation(
            firstName: $data['firstname'] ?? null,
            lastName: $data['lastname'] ?? null,
            middleName: $data['middlename'] ?? null,
            birthday: $data['birthday'] ?? null,
            sex: $data['sex'] ?? null,
            birthPlace: $data['birthplace'] ?? null,
            username: $data['username'] ?? null,
            password: $data['password'] ?? null,
            passwordConfirm: $data['password_confirm'] ?? null,
        );

        //validate the input data
        $result = $this->validator->validate($formData);

        if (!$result->isValid()) {
            // Validation failed - re-render form with errors
            return $this->viewRenderer
                ->withLayout('@src/Web/Shared/Layout/Dashboard/sidebar.php')
                ->render(
                    __DIR__ . '/../View/UserCreationForm.php',
                    [
                        'errors' => $result->getErrors(),
                        'formData' => $formData, // Re-populate form with submitted values
                    ]
                );
        }

        if($formData->password !== $formData->passwordConfirm) {
            return $this->viewRenderer
                ->withLayout('@src/Web/Shared/Layout/Dashboard/sidebar.php')
                ->render(
                    __DIR__ . '/../View/UserCreationForm.php',
                    [
                        'error' => 'Passwords do not match',
                        'formData' => $formData, // Re-populate form with submitted values
                    ]
                );
        }

        //create query
        $this->db->createCommand()->insert('er_person', [
            'firstName' => $formData->firstName,
            'lastName' => $formData->lastName,
            'middleName' => $formData->middleName,
            'birthday' => $formData->birthday,
            'birthPlace' => $formData->birthPlace,
            'sex' => $formData->sex,
        ])->execute();

        $personId = $this->db->getLastInsertID();

        $this->db->createCommand()->insert('sys_user', [
            'username' => $formData->username,
            'password' => password_hash(
                $formData->password,
                PASSWORD_DEFAULT
            ),
            'person_id' => $personId,
            'date_updated' => date('Y-m-d H:i:s'),
            'is_active' => 1,
        ])->execute();

        return $this->viewRenderer
            ->withLayout('@src/Web/Shared/Layout/Dashboard/sidebar.php')
            ->render(
                __DIR__ . '/../View/UserCreationForm.php',
                [
                    'success' => 'User created successfully',
                ],
            );
    }

    public function update(#[RouteArgument] string $id): ResponseInterface
    {
        if($this->sessionCheck->isUserLoggedIn() === false) {
            return $this->sessionCheck->returnLogin();
        }

        // Fetch user data from the database
        $user = $this->db
            ->createCommand(
                'SELECT
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
                WHERE u.id = :id'
            )
            ->bindValue(':id', $id)
            ->queryOne();

        //     var_dump($user); // Debugging line to check the fetched user data
        // die;

        if (!$user) {
            return $this->viewRenderer
                ->withLayout('@src/Web/Shared/Layout/Dashboard/sidebar.php')
                ->render(
                    __DIR__ . '/../View/UserCreationForm.php',
                    [
                        'error' => 'User not found',
                    ],
                );
        }


        // Render the form with existing user data for editing
        return $this->viewRenderer
            ->withLayout('@src/Web/Shared/Layout/Dashboard/sidebar.php')
            ->render(
                __DIR__ . '/../View/UserCreationForm.php',
                [
                    'formData' => $user,
                    'userId' => $id,
                    'isUpdate' => true, // Flag to indicate this is an update operation
                ],
            );
    }

    public function updateSubmit(ServerRequestInterface $request, #[RouteArgument] string $id): ResponseInterface
    {
        $data = $request->getParsedBody();

        if($this->sessionCheck->isUserLoggedIn() === false) {
            return $this->sessionCheck->returnLogin();
        }

        if(!$data) {
            return $this->viewRenderer
                ->withLayout('@src/Web/Shared/Layout/Dashboard/sidebar.php')
                ->render(
                    __DIR__ . '/../View/UserCreationForm.php',
                    [
                        'error' => 'No data received',
                    ],
                );
        }

        // Fetch existing user data to get the person_id
        $user = $this->db
            ->createCommand('SELECT * FROM sys_user WHERE id = :id')
            ->bindValue(':id', $id)
            ->queryOne();


        $formData = [
            'id' => (int) $id,
            'firstName' => $data['firstname'] ?? '',
            'lastName' => $data['lastname'] ?? '',
            'middleName' => $data['middlename'] ?? '',
            'birthday' => $data['birthday'] ?? '',
            'birthPlace' => $data['birthplace'] ?? '',
            'sex' => $data['sex'] ?? '',
        ];   

        if (!$user) {
            return $this->viewRenderer
                ->withLayout('@src/Web/Shared/Layout/Dashboard/sidebar.php')
                ->render(
                    __DIR__ . '/../View/UserCreationForm.php',
                    [
                        'error' => 'User not found',
                    ],
                );
        }

        // Update person data
        $this->db->createCommand()->update('er_person', [
            'firstName' => $data['firstname'] ?? null,
            'lastName' => $data['lastname'] ?? null,
            'middleName' => $data['middlename'] ?? null,
            'birthday' => $data['birthday'] ?? null,
            'birthPlace' => $data['birthplace'] ?? null,
            'sex' => $data['sex'] ?? null,
        ], ['id' => $user['person_id']])->execute();

        // Update user data
        $this->db->createCommand()->update('sys_user', [
            'date_updated' => date('Y-m-d H:i:s'),
        ], ['id' => $id])->execute();

        return $this->viewRenderer
            ->withLayout('@src/Web/Shared/Layout/Dashboard/sidebar.php')
            ->render(
                __DIR__ . '/../View/UserCreationForm.php',
                [
                    'success' => 'User updated successfully',
                    'formData' => $formData, // Re-populate form with submitted values
                    'userId' => $id,
                    'isUpdate' => true, // Flag to indicate this is an update operation
                ],
            );
    }

    // show the list of users
    public function view(): ResponseInterface
    {
        if($this->sessionCheck->isUserLoggedIn() === false) {
            return $this->sessionCheck->returnLogin();
        }

        $users = $this->db
            ->createCommand('SELECT * FROM sys_user')
            ->queryAll();

        return $this->viewRenderer
            ->withLayout('@src/Web/Shared/Layout/Dashboard/sidebar.php')
            ->render(
                __DIR__ . '/../View/UserList.php',
                [
                    'users' => $users,
                ],
            );
        
    }

}
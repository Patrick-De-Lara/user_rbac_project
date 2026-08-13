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

final class CreateUserAction
{
    
    public function __construct(
        private WebViewRenderer $viewRenderer,
        private UrlGeneratorInterface $urlGenerator,
        private ResponseFactoryInterface $responseFactory,
        private SessionInterface $session,
        private ConnectionInterface $db,
        private Validator $validator,
    ) {
    }

    // show creation user
    public function render(): ResponseInterface
    {
        if(!$this->session->get('user_id')) {
            $url = $this->urlGenerator->generate('login');
            return $this->responseFactory
                ->createResponse(302)
                ->withHeader('Location', $url);
        }
        return $this->viewRenderer
            ->withLayout('@src/Web/Shared/Layout/Dashboard/sidebar.php')
            ->render(
                __DIR__ . '/../View/UserCreationForm.php',
            );
    }

    //  create user method
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();

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

    // show the list of users
    public function view(): ResponseInterface
    {
        if(!$this->session->get('user_id')) {
            $url = $this->urlGenerator->generate('login');
            return $this->responseFactory
                ->createResponse(302)
                ->withHeader('Location', $url);
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
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
use Modules\Service\SessionChecker;

final class RoleAction
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private UrlGeneratorInterface $urlGenerator,
        private WebViewRenderer $viewRenderer,
        private SessionInterface $session,
        private ConnectionInterface $db,
        private SessionChecker $sessionCheck,
    ) {}

    // =========================================================
    // ROLE MANAGEMENT PAGE
    // GET /role-list
    // =========================================================

    public function render(): ResponseInterface
    {
        if ($this->sessionCheck->isUserLoggedIn() === false) {
            return $this->sessionCheck->returnLogin();
        }

        // All roles defined in the system
        $allRoles = $this->db->createCommand('
            SELECT id, code, description, is_active
            FROM sys_user_role
            ORDER BY code ASC
        ')->queryAll();

        // All users with their personal info joined
        $allUsers = $this->db->createCommand('
            SELECT
                u.id,
                u.username,
                u.is_active,
                p.firstName,
                p.middleName,
                p.lastName
            FROM sys_user u
            LEFT JOIN er_person p ON p.id = u.person_id
            ORDER BY p.lastName ASC, p.firstName ASC
        ')->queryAll();

        return $this->viewRenderer
            ->withLayout('@src/Web/Shared/Layout/Dashboard/sidebar.php')
            ->render(__DIR__ . '/../View/UserRoleEdit.php', [
                'allRoles' => $allRoles,
                'allUsers' => $allUsers,
                'flash'    => $this->pullFlash(),
            ]);
    }

    // =========================================================
    // GET ASSIGNED ROLES FOR A USER (AJAX/JSON)
    // GET /role-list/user-roles?user_id=1
    //
    // Returns JSON used by JS to highlight which roles
    // are already assigned when a user is selected.
    // =========================================================

    public function getUserRoles(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->sessionCheck->isUserLoggedIn() === false) {
            return $this->jsonResponse(['error' => 'Unauthorized'], 401);
        }

        $userId = (int) ($request->getQueryParams()['user_id'] ?? 0);

        if ($userId === 0) {
            return $this->jsonResponse(['error' => 'Invalid user_id'], 400);
        }

        $assignedRoles = $this->db->createCommand('
            SELECT r.id, r.code, r.description
            FROM sys_user_has_role uhr
            INNER JOIN sys_user_role r ON r.id = uhr.role_id
            WHERE uhr.user_id = :user_id
            ORDER BY r.code ASC
        ')
            ->bindValue(':user_id', $userId)
            ->queryAll();

        return $this->jsonResponse([
            'assignedRoleIds' => array_column($assignedRoles, 'id'),
            'assignedRoles'   => $assignedRoles,
        ]);
    }

    // =========================================================
    // SAVE ROLE ASSIGNMENTS FOR A USER
    // POST /role-list/save
    //
    // Body: user_id, role_ids[] (array of role IDs to assign)
    // Replaces ALL existing role assignments for that user.
    // =========================================================

    public function save(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->sessionCheck->isUserLoggedIn() === false) {
            return $this->sessionCheck->returnLogin();
        }

        $body = $request->getParsedBody();

        // The role-assignment UI sends JSON with fetch. Decode it when no
        // global request-body parser has populated the PSR-7 parsed body.
        if (!is_array($body)) {
            $body = [];
        }

        if (
            $body === []
            && str_starts_with(strtolower($request->getHeaderLine('Content-Type')), 'application/json')
        ) {
            try {
                $decoded = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Invalid JSON request body.',
                ], 400);
            }

            if (!is_array($decoded)) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'JSON request body must be an object.',
                ], 400);
            }

            $body = $decoded;
        }

        $userId = (int) ($body['user_id'] ?? 0);

        if ($userId === 0) {
            $this->setFlash('error', 'Invalid user selected.');
            return $this->redirect('/role-list');
        }

        // role_ids comes as an array from JS fetch body
        $roleIds = array_map('intval', (array) ($body['role_ids'] ?? []));

        // Wrap in transaction — delete + insert must both succeed or both fail
        $transaction = $this->db->beginTransaction();

        try {
            // Remove all current assignments for this user
            $this->db->createCommand('
                DELETE FROM sys_user_has_role WHERE user_id = :user_id
            ')
                ->bindValue(':user_id', $userId)
                ->execute();

            // Insert the new set
            if ($roleIds !== []) {
                $now = date('Y-m-d H:i:s');

                foreach ($roleIds as $roleId) {
                    $this->db->createCommand('
                        INSERT INTO sys_user_has_role (user_id, role_id, date_updated)
                        VALUES (:user_id, :role_id, :date_updated)
                    ')
                        ->bindValues([
                            ':user_id'      => $userId,
                            ':role_id'      => $roleId,
                            ':date_updated' => $now,
                        ])
                        ->execute();
                }
            }

            $transaction->commit();
            return $this->jsonResponse(['success' => true, 'message' => 'Roles saved successfully.']);

        } catch (\Throwable $e) {
            $transaction->rollBack();
            return $this->jsonResponse(['success' => false, 'message' => 'Failed to save roles.'], 500);
        }
    }

    // =========================================================
    // ROLE CRUD — CREATE ROLE
    // GET  /role-list/create  → show form
    // POST /role-list/create  → save new role
    // =========================================================

    public function create(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->sessionCheck->isUserLoggedIn() === false) {
            return $this->sessionCheck->returnLogin();
        }

        $errors = [];
        $fields = ['code' => '', 'description' => '', 'is_active' => 1];

        if ($request->getMethod() === 'POST') {
            $body = (array) $request->getParsedBody();

            $fields = [
                'code'        => trim((string) ($body['code'] ?? '')),
                'description' => trim((string) ($body['description'] ?? '')),
                'is_active'   => isset($body['is_active']) ? 1 : 0,
            ];

            $errors = $this->validateRole($fields);

            if ($errors === []) {
                $exists = (int) $this->db->createCommand('
                    SELECT COUNT(*) FROM sys_user_role WHERE code = :code
                ')
                    ->bindValue(':code', $fields['code'])
                    ->queryScalar();

                if ($exists > 0) {
                    $errors['code'] = 'Role code already exists.';
                }
            }

            if ($errors === []) {
                $this->db->createCommand('
                    INSERT INTO sys_user_role (code, description, is_active)
                    VALUES (:code, :description, :is_active)
                ')
                    ->bindValues([
                        ':code'        => $fields['code'],
                        ':description' => $fields['description'],
                        ':is_active'   => $fields['is_active'],
                    ])
                    ->execute();

                $this->setFlash('success', 'Role created successfully.');
                return $this->redirect('/role-list');
            }
        }

        return $this->viewRenderer
            ->withLayout('@src/Web/Shared/Layout/Dashboard/sidebar.php')
            ->render(__DIR__ . '/../View/RoleEdit.php', [
                'title'       => 'Create Role',
                'submitLabel' => 'Create Role',
                'actionUrl'   => '/role-list/create',
                'fields'      => $fields,
                'errors'      => $errors,
                'flash'       => $this->pullFlash(),
            ]);
    }

    // =========================================================
    // ROLE CRUD — EDIT ROLE
    // GET  /role-list/edit?id=1  → show edit form
    // POST /role-list/edit?id=1  → save changes
    // =========================================================

    public function edit(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->sessionCheck->isUserLoggedIn() === false) {
            return $this->sessionCheck->returnLogin();
        }

        $id   = (int) ($request->getQueryParams()['id'] ?? 0);
        $role = $this->findRoleById($id);

        if ($role === null) {
            $this->setFlash('error', 'Role not found.');
            return $this->redirect('/role-list');
        }

        $errors = [];
        $fields = [
            'code'        => $role['code'],
            'description' => $role['description'],
            'is_active'   => $role['is_active'],
        ];

        if ($request->getMethod() === 'POST') {
            $body = (array) $request->getParsedBody();

            $fields = [
                'code'        => trim((string) ($body['code'] ?? '')),
                'description' => trim((string) ($body['description'] ?? '')),
                'is_active'   => isset($body['is_active']) ? 1 : 0,
            ];

            $errors = $this->validateRole($fields);

            if ($errors === []) {
                $exists = (int) $this->db->createCommand('
                    SELECT COUNT(*) FROM sys_user_role
                    WHERE code = :code AND id != :id
                ')
                    ->bindValues([':code' => $fields['code'], ':id' => $id])
                    ->queryScalar();

                if ($exists > 0) {
                    $errors['code'] = 'Role code already exists.';
                }
            }

            if ($errors === []) {
                $this->db->createCommand('
                    UPDATE sys_user_role
                    SET code = :code,
                        description = :description,
                        is_active = :is_active
                    WHERE id = :id
                ')
                    ->bindValues([
                        ':code'        => $fields['code'],
                        ':description' => $fields['description'],
                        ':is_active'   => $fields['is_active'],
                        ':id'          => $id,
                    ])
                    ->execute();

                $this->setFlash('success', 'Role updated successfully.');
                return $this->redirect('/role-list');
            }
        }

        return $this->viewRenderer
            ->withLayout('@src/Web/Shared/Layout/Dashboard/sidebar.php')
            ->render(__DIR__ . '/../View/RoleEdit.php', [
                'title'       => 'Edit Role',
                'submitLabel' => 'Save Changes',
                'actionUrl'   => '/role-list/edit?id=' . $id,
                'fields'      => $fields,
                'errors'      => $errors,
                'flash'       => $this->pullFlash(),
                'allActions'  => $this->db->createCommand('SELECT id, code, description FROM sys_user_action WHERE is_active = 1 ORDER BY code ASC')->queryAll(),
                'roleActions' => $this->db->createCommand('SELECT action_id FROM sys_user_role_has_action WHERE role_id = :rid')->bindValue(':rid', $id)->queryAll(),
            ]);
    }

    // =========================================================
    // ROLE CRUD — DELETE ROLE
    // POST /role-list/delete
    // Body: id
    // =========================================================

    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->sessionCheck->isUserLoggedIn() === false) {
            return $this->sessionCheck->returnLogin();
        }

        $body = (array) $request->getParsedBody();
        $id   = (int) ($body['id'] ?? 0);

        if ($id === 0) {
            $this->setFlash('error', 'Invalid role.');
            return $this->redirect('/role-list');
        }

        // Safety check — do not delete if users still have this role
        $inUse = (int) $this->db->createCommand('
            SELECT COUNT(*) FROM sys_user_has_role WHERE role_id = :id
        ')
            ->bindValue(':id', $id)
            ->queryScalar();

        if ($inUse > 0) {
            $this->setFlash('error', 'Cannot delete — role is still assigned to ' . $inUse . ' user(s).');
            return $this->redirect('/role-list');
        }

        $this->db->createCommand('DELETE FROM sys_user_role WHERE id = :id')
            ->bindValue(':id', $id)
            ->execute();

        $this->setFlash('success', 'Role deleted successfully.');
        return $this->redirect('/role-list');
    }

    // =========================================================
    // ROLE ACTION PERMISSIONS — SAVE
    // POST /role-list/save-actions
    // Body JSON: { role_id: int, action_ids: int[] }
    // Replaces all action assignments for this role in a transaction.
    // =========================================================

    public function saveActions(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->sessionCheck->isUserLoggedIn() === false) {
            return $this->jsonResponse(['error' => 'Unauthorized'], 401);
        }

        $body = $request->getParsedBody();

        // The permissions UI sends an application/json fetch request. This
        // application does not have a global JSON body parser, so decode it
        // here when the PSR-7 request has no parsed form body.
        if (!is_array($body)) {
            $body = [];
        }

        if (
            $body === []
            && str_starts_with(strtolower($request->getHeaderLine('Content-Type')), 'application/json')
        ) {
            try {
                $decoded = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Invalid JSON request body.',
                ], 400);
            }

            if (!is_array($decoded)) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'JSON request body must be an object.',
                ], 400);
            }

            $body = $decoded;
        }

        $roleId = (int) ($body['role_id'] ?? 0);

        if ($roleId == 0) {
            return $this->jsonResponse(['success' => false, 'message' => 'Invalid role.'], 400);
        }

        $actionIds  = array_map('intval', (array) ($body['action_ids'] ?? []));
        $authorId   = (int) $this->session->get('user_id');
        $now        = date('Y-m-d H:i:s');
        $transaction = $this->db->beginTransaction();

        try {
            // Remove all current action assignments for this role
            $this->db->createCommand('
                DELETE FROM sys_user_role_has_action WHERE role_id = :role_id
            ')
                ->bindValue(':role_id', $roleId)
                ->execute();

            // Insert the new set
            foreach ($actionIds as $actionId) {
                $this->db->createCommand('
                    INSERT INTO sys_user_role_has_action (role_id, action_id, author_id, date_updated)
                    VALUES (:role_id, :action_id, :author_id, :date_updated)
                ')
                    ->bindValues([
                        ':role_id'      => $roleId,
                        ':action_id'    => $actionId,
                        ':author_id'    => $authorId,
                        ':date_updated' => $now,
                    ])
                    ->execute();
            }

            $transaction->commit();
            $this->setFlash('success', 'Permissions saved successfully.');

            return $this->jsonResponse([
                'success' => true,
                'message' => 'Permissions saved successfully.',
                'count'   => count($actionIds),
                'redirect' => '/role-list/edit?id=' . $roleId,
            ]);

        } catch (\Throwable $e) {
            $transaction->rollBack();
            return $this->jsonResponse(['success' => false, 'message' => 'Failed to save permissions.'], 500);
        }
    }

    // =========================================================
    // PRIVATE HELPERS
    // =========================================================

    private function findRoleById(int $id): ?array
    {
        if ($id === 0) {
            return null;
        }

        $result = $this->db->createCommand('
            SELECT id, code, description, is_active
            FROM sys_user_role WHERE id = :id
        ')
            ->bindValue(':id', $id)
            ->queryOne();

        return $result ?: null;
    }

    private function validateRole(array $fields): array
    {
        $errors = [];

        if ($fields['code'] === '') {
            $errors['code'] = 'Role code is required.';
        } elseif (strlen($fields['code']) > 20) {
            $errors['code'] = 'Role code must be 20 characters or fewer.';
        }

        if ($fields['description'] === '') {
            $errors['description'] = 'Description is required.';
        } elseif (strlen($fields['description']) > 100) {
            $errors['description'] = 'Description must be 100 characters or fewer.';
        }

        return $errors;
    }

    private function jsonResponse(mixed $data, int $status = 200): ResponseInterface
    {
        $response = $this->responseFactory->createResponse($status);
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function redirect(string $url): ResponseInterface
    {
        return $this->responseFactory->createResponse(302)->withHeader('Location', $url);
    }

    private function setFlash(string $type, string $message): void
    {
        $this->session->set('flash_' . $type, $message);
    }

    private function pullFlash(): array
    {
        $flash = [
            'success' => $this->session->get('flash_success'),
            'error'   => $this->session->get('flash_error'),
        ];
        $this->session->remove('flash_success');
        $this->session->remove('flash_error');
        return $flash;
    }
}

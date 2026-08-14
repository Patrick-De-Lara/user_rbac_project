<?php

declare(strict_types=1);

namespace Modules\HR\Trait;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Session\SessionInterface;

/**
 * ActionHelper
 *
 * A trait providing common HTTP/response utilities for action classes.
 * Keeps action classes clean by centralizing response building,
 * redirects, and flash messaging in one reusable place.
 *
 * Requirements — the action using this trait must have these
 * properties declared in its constructor:
 *
 *   private ResponseFactoryInterface $responseFactory,
 *   private SessionInterface $session,
 */
trait ActionHelper
{
    // =========================================================
    // RESPONSE HELPERS
    // =========================================================

    /**
     * Return a JSON response.
     *
     * Usage:
     *   return $this->jsonResponse(['success' => true]);
     *   return $this->jsonResponse(['error' => 'Not found'], 404);
     */

    private function jsonResponse(mixed $data, int $status = 200): ResponseInterface
    {
        $response = $this->responseFactory->createResponse($status);
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Return an HTML response.
     *
     * Usage:
     *   return $this->htmlResponse('<h1>Hello</h1>');
     */
    private function htmlResponse(string $html, int $status = 200): ResponseInterface
    {
        $response = $this->responseFactory->createResponse($status);
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * Return a redirect response.
     *
     * Usage:
     *   return $this->redirect('/role-list');
     *   return $this->redirect('/login', 301);
     */
    private function redirect(string $url, int $status = 302): ResponseInterface
    {
        return $this->responseFactory
            ->createResponse($status)
            ->withHeader('Location', $url);
    }

    // =========================================================
    // FLASH MESSAGE HELPERS
    // =========================================================

    /**
     * Store a flash message in the session.
     * Supports 'success', 'error', 'warning', 'info'.
     *
     * Usage:
     *   $this->flash('success', 'User created successfully.');
     *   $this->flash('error', 'Something went wrong.');
     */
    private function flash(string $type, string $message): void
    {
        $this->session->set('flash_' . $type, $message);
    }

    /**
     * Pull all flash messages from the session (reads and clears them).
     * Returns an array with keys: success, error, warning, info.
     *
     * Usage in action:
     *   'flash' => $this->pullFlash()
     *
     * Usage in view:
     *   <?php if (!empty($flash['success'])): ?>
     *     <div class="..."> <?= $flash['success'] ?> </div>
     *   <?php endif; ?>
     */
    private function pullFlash(): array
    {
        $types = ['success', 'error', 'warning', 'info'];
        $flash = [];

        foreach ($types as $type) {
            $key = 'flash_' . $type;
            $flash[$type] = $this->session->get($key);
            $this->session->remove($key);
        }

        return $flash;
    }

    // =========================================================
    // REQUEST HELPERS
    // =========================================================

    /**
     * Safely cast request parsed body to array.
     * Prevents errors when body is null or not an array.
     *
     * Usage:
     *   $body = $this->body($request);
     *   $name = $body['name'] ?? '';
     */
    private function body(\Psr\Http\Message\ServerRequestInterface $request): array
    {
        $parsed = $request->getParsedBody();
        return is_array($parsed) ? $parsed : [];
    }

    /**
     * Get a route/query param as a positive int, or null if missing/invalid.
     *
     * Usage:
     *   $id = $this->intParam($request, 'id');
     *   if ($id === null) { return $this->redirect('/list'); }
     */
    private function intParam(\Psr\Http\Message\ServerRequestInterface $request, string $key): ?int
    {
        $query = $request->getQueryParams();
        $body  = $this->body($request);
        $value = $query[$key] ?? $body[$key] ?? null;

        if ($value === null || $value === '') {
            return null;
        }

        return ctype_digit((string) $value) ? (int) $value : null;
    }
}
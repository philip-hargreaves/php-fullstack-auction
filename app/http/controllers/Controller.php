<?php
namespace app\http\controllers;

use infrastructure\Utilities;

abstract class Controller
{
    protected function view(string $view, array $data = []): void
    {
        extract($data);
        require Utilities::basePath("views/{$view}.view.php");
    }

    protected function redirect(string $path): never
    {
        header("Location: {$path}");
        exit;
    }

    protected function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function ensurePost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
        }
    }

    protected function ensureLoggedIn(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'You must be logged in.';
            $this->redirect('/');
        }
    }

    protected function ensureRole(string $role): void
    {
        $roles = $_SESSION['role_names'] ?? [];
        if (!in_array($role, $roles, true)) {
            $_SESSION['error'] = 'You do not have permission to access this page.';
            $this->redirect('/');
        }
    }

    protected function userId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    protected function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    protected function hasRole(string $role): bool
    {
        $roles = $_SESSION['role_names'] ?? [];
        return in_array($role, $roles, true);
    }
}
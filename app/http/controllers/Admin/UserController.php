<?php
namespace app\http\controllers\Admin;

use app\http\controllers\Controller;
use infrastructure\DIContainer;
use infrastructure\Request;
use app\services\AuthService;

class UserController extends Controller
{
    private $userServ;

    public function __construct()
    {
        $this->userServ = DIContainer::get('userServ');
    }

    /** PUT /admin/users/{id}/status */
    public function updateStatus(array $params = []): void
    {
        $this->requireAdmin();
        $this->ensurePost();

        $targetUserId = (int)($params['id'] ?? Request::post('user_id'));
        $isActive = Request::post('is_active');

        if ($targetUserId <= 0) {
            $_SESSION['admin_error'] = 'Invalid user ID.';
            $this->redirectBack();
        }

        $targetUser = $this->userServ->getUserAccount($targetUserId);

        // Prevent modifying admin users
        if ($targetUser !== null && $targetUser->isAdmin()) {
            $_SESSION['admin_error'] = 'Admin accounts cannot be modified.';
            $this->redirectBack();
        }

        $isActiveBool = ($isActive === '1' || $isActive === 'true' || $isActive === true);
        $currentAdminId = AuthService::getUserId();

        $result = $this->userServ->updateUserActiveStatus($targetUserId, $isActiveBool, $currentAdminId);

        if ($result['success']) {
            $_SESSION['admin_success'] = $result['message'];
        } else {
            $_SESSION['admin_error'] = $result['message'] ?? 'Failed to update user status.';
        }

        $this->redirectBack();
    }

    /** PUT /admin/users/{id}/roles */
    public function manageRole(array $params = []): void
    {
        $this->requireAdmin();
        $this->ensurePost();

        $targetUserId = (int)($params['id'] ?? Request::post('user_id'));
        $roleName = Request::post('role_name');
        $action = Request::post('action');

        if ($targetUserId <= 0) {
            $_SESSION['admin_error'] = 'Invalid user ID.';
            $this->redirectBack();
        }

        if (empty($roleName)) {
            $_SESSION['admin_error'] = 'Role name is required.';
            $this->redirectBack();
        }

        if (empty($action) || !in_array($action, ['assign', 'revoke'], true)) {
            $_SESSION['admin_error'] = 'Invalid action.';
            $this->redirectBack();
        }

        $targetUser = $this->userServ->getUserAccount($targetUserId);

        // Prevent modifying admin users
        if ($targetUser !== null && $targetUser->isAdmin()) {
            $_SESSION['admin_error'] = 'Admin accounts cannot be modified.';
            $this->redirectBack();
        }

        $currentAdminId = AuthService::getUserId();

        if ($action === 'assign') {
            $result = $this->userServ->assignUserRole($targetUserId, $roleName);
        } else {
            $result = $this->userServ->revokeUserRole($targetUserId, $roleName, $currentAdminId);
        }

        if ($result['success']) {
            $_SESSION['admin_success'] = $result['message'];
        } else {
            $_SESSION['admin_error'] = $result['message'] ?? 'Failed to manage user role.';
        }

        $this->redirectBack();
    }

    /** Require admin role */
    private function requireAdmin(): void
    {
        if (!AuthService::isLoggedIn() || !AuthService::hasRole('admin')) {
            $this->redirect('/');
        }
    }

    /** Redirect back to admin with tab/page preserved */
    private function redirectBack(): void
    {
        $tab = Request::post('tab', 'dashboard');
        $page = Request::post('page', '1');

        $url = '/admin';
        if ($tab === 'users') {
            $url .= '?tab=users';
            if ($page !== '1') {
                $url .= '&page=' . urlencode($page);
            }
        }

        $this->redirect($url);
    }
}


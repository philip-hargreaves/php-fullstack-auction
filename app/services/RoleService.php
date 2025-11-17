<?php
namespace app\services;
use app\repositories\UserRepository;
use app\repositories\RoleRepository;
use app\repositories\UserRoleRepository;

class RoleService {
    private RoleRepository $roleRepository;
    private UserRoleRepository $userRoleRepository;
    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository,
        RoleRepository $roleRepository,
        UserRoleRepository $userRoleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->userRoleRepository = $userRoleRepository;
    }

    public function upgradeToSeller(int $userId): array
    {
        // Fetch and check user exists
        $user = $this->userRepository->getById($userId);
        if ($user === null) {
            return $this->response('User not found.');
        }

        if ($user->isSeller()) {
            return $this->response('You are already a seller!');
        }

        // Get seller role from database
        $sellerRole = $this->roleRepository->getByName('seller');
        if ($sellerRole === null) {
            return $this->response('Seller role not found in system. Please contact support.');
        }

        // Assign seller role
        try {
            $this->userRoleRepository->assignRole($userId, $sellerRole);

            return $this->response('Congratulations! You are now a seller. You can create auctions and manage your listings.', true);
        } catch (\Exception $e) {
            // Log error for debugging

            return $this->response('Failed to upgrade account. Please try again later.');
        }
    }

    // Return a response (success or failure)
    private function response(string $message, bool $isSuccess = false): array
    {
        return [
            'success' => $isSuccess,
            'message' => $message,
        ];
    }
}
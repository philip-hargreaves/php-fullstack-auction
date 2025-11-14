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
            return $this->fail(['User not found.']);
        }

        if ($user->isSeller()) {
            return $this->fail(['You are already a seller!']);
        }

        // Get seller role from database
        $sellerRole = $this->roleRepository->getByName('seller');
        if ($sellerRole === null) {
            return $this->fail(['Seller role not found in system. Please contact support.']);
        }

        // Assign seller role
        try {
            $this->userRoleRepository->assignRole($userId, $sellerRole);

            return [
                'success' => true,
                'errors'  => [],
                'message' => 'Congratulations! You are now a seller. You can create auctions and manage your listings.'
            ];
        } catch (\Exception $e) {
            // Log error for debugging

            return $this->fail(['Failed to upgrade account. Please try again later.']);
        }
    }

    // Return a failure response
    private function fail(array $errors): array
    {
        return [
            'success' => false,
            'errors'  => $errors,
            'message' => null,
        ];
    }
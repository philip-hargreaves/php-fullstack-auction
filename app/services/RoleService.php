<?php
namespace app\services;
use app\repositories\UserRepository;
use app\repositories\RoleRepository;
use app\repositories\UserRoleRepository;
use infrastructure\Database;
use infrastructure\Utilities;

class RoleService {
    private RoleRepository $roleRepository;
    private UserRoleRepository $userRoleRepository;
    private UserRepository $userRepository;
    private Database $db;

    public function __construct(
        UserRepository $userRepository,
        RoleRepository $roleRepository,
        UserRoleRepository $userRoleRepository,
        Database $db)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->db = $db;
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

        // Get the DB connection
        $pdo = $this->db->connection;

        // Wrap role assignment in a transaction
        try {
            Utilities::beginTransaction($pdo);

            // Assign seller role
            $this->userRoleRepository->assignRole($userId, $sellerRole);

            // Commit the transaction
            $pdo->commit();

            return $this->response('Congratulations! You are now a seller. You can create auctions and manage your listings.', true);
        } catch (\Throwable $e) {
            // Roll back the transaction to avoid partial state
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

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
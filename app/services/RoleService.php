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
            return Utilities::creationResult('User not found.', false, null);
        }

        if ($user->isSeller()) {
            return Utilities::creationResult('You are already a seller!', false, null);
        }

        // Get seller role from database
        $sellerRole = $this->roleRepository->getByName('seller');
        if ($sellerRole === null) {
            return Utilities::creationResult('Seller role not found in system. Please contact support.', false, null);
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

            return Utilities::creationResult('Congratulations! You are now a seller. You can create auctions and manage your listings.', true, null);

        } catch (\Throwable $e) {
            // Roll back the transaction to avoid partial state
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return Utilities::creationResult('Failed to upgrade account. Please try again later.', false, null);
        }
    }

}
<?php

class Database
{
    public $connection;

    public function __construct()
    {
        // Fetch credentials from environment variables set in docker-compose.yml
        $host = getenv('DB_HOST') ?: 'db';
        $port = getenv('DB_PORT') ?: '3306';  # Standard MySQL port
        $dbname = getenv('DB_NAME') ?: 'auction_db';
        $user = getenv('DB_USER') ?: 'user';
        $pass = getenv('DB_PASS') ?: 'password';

        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

        try {
            $options = [
                # Throw error instead of fail silently
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                # Set default fetch mode to associative array
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            # Establish connection
            $this->connection = new PDO($dsn, $user, $pass, $options);

        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    # Prepares and executes query statement
    public function query($query, $params = [])
    {
        $statement = $this->connection->prepare($query);
        $statement->execute($params);
        return $statement;
    }
}
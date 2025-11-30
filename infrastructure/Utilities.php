<?php
namespace infrastructure;
use DateInterval;
use PDO;
use DateTime;

// Utility class for general purpose application helpers.
class Utilities
{
    // Define project's absolute root path
    const BASE_PATH = __DIR__ . "/../";

    // Gets the absolute path relative to the project root
    public static function basePath(string $path = ''): string
    {
        return self::BASE_PATH . $path;
    }

    // Dump and Die" - Displays information about a variable and stops script execution.
    public static function dd(mixed $value): void
    {
        echo '<pre>';
        var_dump($value);
        echo '</pre>';
        die();
    }

    // Formats a DateInterval object into a human-readable time remaining string
    public static function displayTimeRemaining(DateInterval $interval): string
    {
        if ($interval->days == 0 && $interval->h == 0) {
            // Less than one hour remaining: print mins + seconds:
            $time_remaining = $interval->format('%im %Ss');
        } elseif ($interval->days == 0) {
            // Less than one day remaining: print hrs + mins:
            $time_remaining = $interval->format('%hh %im');
        } else {
            // At least one day remaining: print days + hrs:
            $time_remaining = $interval->format('%ad %hh');
        }
        return $time_remaining;
    }

    //Starts a transaction if the PDO connection doesn't already have one
    public static function beginTransaction(PDO $pdo): void
    {
        if (!$pdo->inTransaction()) {
            $pdo->beginTransaction();
        }
    }

    public static function creationResult(string $message, bool $success, $createdObject, array $errors = []): array
    {
        $result = [
            'success' => $success,
            'message'  => $message,
            'object'    => $createdObject,
        ];

        if (!empty($errors)) {
            $result['errors'] = $errors;
        }

        return $result;
    }

    // Safely format dates for input
    public static function formatForInput($date): string {
        if (empty($date)) return '';

        // If it's already a DateTime object
        if ($date instanceof DateTime) {
            return $date->format('Y-m-d\TH:i');
        }

        // If it's a string (e.g., from Database "2025-01-01 12:00:00")
        return date('Y-m-d\TH:i', strtotime($date));
    }
}

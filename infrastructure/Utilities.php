<?php
namespace infrastructure;
use DateInterval;
use PDO;

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
}

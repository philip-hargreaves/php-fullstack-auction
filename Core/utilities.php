<?php

// "Dump and Die"
function dd($value)
{
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
    die();
}

// Time remaining:
function display_time_remaining($interval) {
    if ($interval->days == 0 && $interval->h == 0) {
        // Less than one hour remaining: print mins + seconds:
        $time_remaining = $interval->format('%im %Ss');
    }
    else if ($interval->days == 0) {
        // Less than one day remaining: print hrs + mins:
        $time_remaining = $interval->format('%hh %im');
    }
    else {
        // At least one day remaining: print days + hrs:
        $time_remaining = $interval->format('%ad %hh');
    }
    return $time_remaining;
}
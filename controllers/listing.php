<?php

// 2. Get info from the URL
$item_id = $_GET['item_id'];


// TODO: Use item_id to make a query to the database.

// DELETEME: For now, using placeholder data.
$title = "Placeholder title for item ";
$description = "Description blah blah blah";
$current_price = 30.50;
$num_bids = 1;
$end_time = new DateTime('2020-11-02T00:00:00');

// TODO: Note: Auctions that have ended may pull a different set of data,
//       like whether the auction ended in a sale or was cancelled due
//       to lack of high-enough bids. Or maybe not.

// Calculate time to auction end:
$now = new DateTime();

if ($now < $end_time) {
    $time_to_end = date_diff($now, $end_time);
    $time_remaining = ' (in ' . display_time_remaining($time_to_end) . ')';
}

// TODO: If the user has a session, use it to make a query to the database
//       to determine if the user is already watching this item.
//       For now, this is hardcoded.
$has_session = true;
$watching = false;


// 4. At the very end, load the "View"
// This "passes" all the single-item variables ($item_id, $title,
// $description, etc.) into the 'views/listing.view.php' file.
require base_path('views/listing.view.php');
?>
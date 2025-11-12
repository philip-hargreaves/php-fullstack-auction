<?php

// Start session to handle errors
session_start();

// Get the form data using the static Request class
$bid_amount = Request::post('bid_amount');
$auction_id = Request::post('auction_id');
$user_id = $_SESSION['user_id'] ?? null;

// Validate data
$errors = [];

// A. Check if user is logged in
if (!$user_id) {
    $errors[] = 'You must be logged in to place a bid.';
}

// B. Check if data is valid
if (!is_numeric($bid_amount) || $bid_amount <= 0) {
    $errors[] = 'Your bid amount is not valid.';
}

// C. Check business logic
// You'll need to connect to your database here.
// This is a simplified example.
// $db = new Database($config);

// (MODEL) Fetch the auction's *actual* current bid from the DB
// **Never trust data sent from the form (like $highestBid)**
// $auction = $db->query("SELECT * FROM auctions WHERE id = ?", [$auction_id])->find();
// $current_highest_bid = $auction['current_bid'] ?? 0;

/* --- Start Example DB Logic --- */
// This is placeholder logic. Replace with your actual database call.
//$current_highest_bid = 100.00; // Example: $auction['current_bid'];
//if ($auction_id == '123') { // Example: $auction is found
//    if ($bid_amount <= $current_highest_bid) {
//        $errors[] = 'Your bid must be higher than the current highest bid of £' . number_format($current_highest_bid, 2);
//    }
//} else {
//    $errors[] = 'Auction not found.';
//}
/* --- End Example DB Logic --- */


// 3. PROCESS (If validation passed)
if (empty($errors)) {
    // Validation passed!

    // (MODEL) Save the new bid to the database
    // $db->query("INSERT INTO bids (auction_id, user_id, amount) VALUES (?, ?, ?)", [
    //     $auction_id,
    //     $user_id,
    //     $bid_amount
    // ]);

    // (MODEL) Update the auction's main price
    // $db->query("UPDATE auctions SET current_bid = ? WHERE id = ?", [
    //     $bid_amount,
    //     $auction_id
    // ]);

    // 4. REDIRECT (on success)
    // Redirect back to the auction page.
    header('Location: /auction?id=' . $auction_id);
    exit();

} else {
    // Validation failed!

    // Store errors in the session so the view can show them
    $_SESSION['errors'] = $errors;

    // 4. REDIRECT (on failure)
    // Send the user back to the form
    header('Location: /auction?id=' . $auction_id);
    exit();
}
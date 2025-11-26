<?php
// public/ajax/upload-image.php

// 1. Send JSON headers immediately
header('Content-Type: application/json');

// --- CONFIGURATION ---
$IMGBB_API_KEY = 'c26fe5fca2acb7c3b01ada08ba8d5e7a';

// 2. Basic Validation
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (!isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded']);
    exit;
}

// 3. Check for upload errors
if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(500);
    echo json_encode(['error' => 'Upload failed with error code: ' . $_FILES['file']['error']]);
    exit;
}

// 4. Security: Validate File Type
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($_FILES['file']['tmp_name']);

if (!in_array($mimeType, $allowedTypes)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file type. Only JPG, PNG, GIF, and WEBP allowed.']);
    exit;
}

// ---------------------------------------------------------
//  ImgBB Upload Logic
// ---------------------------------------------------------

try {
    // Read file content
    $image_data = file_get_contents($_FILES['file']['tmp_name']);

    // Initialize cURL
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.imgbb.com/1/upload?key=' . $IMGBB_API_KEY, // ImgBB Endpoint
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => array(
            'image' => base64_encode($image_data), // ImgBB requires base64
            'name'  => $_FILES['file']['name']     // Optional: file name
        )
    ));

    // Execute Request
    $response = curl_exec($curl);
    $curlError = curl_error($curl);
    curl_close($curl);

    // Check for cURL errors
    if ($curlError) {
        throw new Exception('cURL Error: ' . $curlError);
    }

    // Decode Response
    $responseBody = json_decode($response, true);

    // Check ImgBB Success flag
    if (isset($responseBody['success']) && $responseBody['success'] === true) {

        // Success! Get the display URL
        // 'url' is the direct link to the image
        $publicUrl = $responseBody['data']['url'];

        echo json_encode([
            'url' => $publicUrl
        ]);

    } else {
        // Handle API Error
        $errorMsg = isset($responseBody['error']['message'])
            ? $responseBody['error']['message']
            : 'Unknown ImgBB error';

        throw new Exception('ImgBB API Failed: ' . $errorMsg);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
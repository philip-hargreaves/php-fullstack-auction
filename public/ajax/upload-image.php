<?php
header('Content-Type: application/json');

if ($_FILES['file']) {
// 1. Send $_FILES['file'] to your Cloud Provider (AWS S3 / Cloudinary)
// $url = CloudService::upload($_FILES['file']);

// For testing, just move to local folder:
$filename = uniqid() . '-' . basename($_FILES['file']['name']);
move_uploaded_file($_FILES['file']['tmp_name'], '../uploads/' . $filename);
$url = '/uploads/' . $filename;

echo json_encode(['url' => $url]);
} else {
http_response_code(400);
echo json_encode(['error' => 'No file provided']);
}
<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Get POST data
$originalUrl = $_POST['url'] ?? '';
$customCode = $_POST['custom'] ?? '';

// Validate URL
if (empty($originalUrl)) {
    echo json_encode(['success' => false, 'error' => 'URL is required']);
    exit;
}

// Basic URL validation
if (!filter_var($originalUrl, FILTER_VALIDATE_URL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid URL format']);
    exit;
}

// Validate custom code if provided
if (!empty($customCode)) {
    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $customCode)) {
        echo json_encode(['success' => false, 'error' => 'Custom code can only contain letters, numbers, hyphens, and underscores']);
        exit;
    }
    
    if (strlen($customCode) < 3 || strlen($customCode) > 50) {
        echo json_encode(['success' => false, 'error' => 'Custom code must be between 3 and 50 characters']);
        exit;
    }
}

// File to store URLs
$urlsFile = 'urls.txt';

// Create file if it doesn't exist
if (!file_exists($urlsFile)) {
    file_put_contents($urlsFile, '');
}

// Read existing URLs
$existingUrls = [];
if (filesize($urlsFile) > 0) {
    $lines = file($urlsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $parts = explode('|', $line, 4);
        if (count($parts) >= 2) {
            $existingUrls[$parts[0]] = $parts[1];
        }
    }
}

// Generate or use custom short code
if (!empty($customCode)) {
    $shortCode = $customCode;
    // Check if custom code already exists
    if (isset($existingUrls[$shortCode])) {
        echo json_encode(['success' => false, 'error' => 'Custom code already exists. Please choose a different one.']);
        exit;
    }
} else {
    // Generate random short code
    do {
        $shortCode = generateRandomCode();
    } while (isset($existingUrls[$shortCode]));
}

// Get current timestamp
$timestamp = date('Y-m-d H:i:s');
$domain = $_SERVER['HTTP_HOST'];

// Store the URL mapping
$urlData = $shortCode . '|' . $originalUrl . '|' . $timestamp . '|' . $_SERVER['REMOTE_ADDR'] . "\n";
file_put_contents($urlsFile, $urlData, FILE_APPEND | LOCK_EX);

// Create the short URL
$shortUrl = "http://$domain/r/$shortCode";

// Log the creation
error_log("URL Shortener: Created $shortCode -> $originalUrl");

// Return success response
echo json_encode([
    'success' => true,
    'shortUrl' => $shortUrl,
    'originalUrl' => $originalUrl,
    'shortCode' => $shortCode,
    'timestamp' => $timestamp
]);

function generateRandomCode($length = 6) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    
    return $randomString;
}
?>

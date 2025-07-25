<?php
require_once 'config.php';

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
$code = $_POST['code'] ?? '';
$language = $_POST['language'] ?? 'auto';

// Validate input
if (empty($code)) {
    echo json_encode(['success' => false, 'error' => 'No code provided']);
    exit;
}

if (strlen($code) > 10000) {
    echo json_encode(['success' => false, 'error' => 'Code too long (maximum 10,000 characters)']);
    exit;
}

// Validate API key
if (!validateApiKey()) {
    echo json_encode(['success' => false, 'error' => 'OpenAI API key not configured. Please add your API key to config.php']);
    exit;
}

// Rate limiting
$clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
if (!checkRateLimit($clientIp, 10, 3600)) { // 10 requests per hour
    echo json_encode(['success' => false, 'error' => 'Rate limit exceeded. Please try again later.']);
    exit;
}

// Prepare the prompt for OpenAI
$languageText = $language === 'auto' ? '' : " (Language: $language)";
$prompt = "Analyze this code$languageText and provide a detailed explanation. Please:

1. Explain what the code does overall
2. Go through it step-by-step, describing what each important line or section does
3. Identify any potential bugs, issues, or security concerns
4. Suggest improvements and best practices
5. Explain the algorithm/logic if applicable

Code to analyze:


<?php
// TechToolsHub Configuration File

// OpenAI API Configuration
// Replace "sk-YOUR-API-KEY-HERE" with your actual OpenAI API key
// You can get your API key from: https://platform.openai.com/api-keys
define("OPENAI_API_KEY", $_ENV['OPENAI_API_KEY'] ?? "sk-YOUR-API-KEY-HERE");

// Database Configuration (if needed in future)
// define("DB_HOST", "localhost");
// define("DB_NAME", "techtoolshub");
// define("DB_USER", "your_username");
// define("DB_PASS", "your_password");

// Site Configuration
define("SITE_URL", $_SERVER['HTTP_HOST'] ?? "yourdomain.com");
define("SITE_NAME", "TechToolsHub");

// Error Reporting (set to false in production)
define("DEBUG_MODE", false);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set timezone
date_default_timezone_set('UTC');

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// CORS headers for API endpoints
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    }
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }
    exit(0);
}

// Function to validate API key
function validateApiKey() {
    if (OPENAI_API_KEY === "sk-YOUR-API-KEY-HERE" || empty(OPENAI_API_KEY)) {
        return false;
    }
    return true;
}

// Function to log errors
function logError($message, $file = 'error.log') {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message\n";
    file_put_contents($file, $logMessage, FILE_APPEND | LOCK_EX);
}

// Rate limiting function (basic implementation)
function checkRateLimit($ip, $maxRequests = 10, $timeWindow = 3600) {
    $rateLimitFile = 'rate_limit.txt';
    
    if (!file_exists($rateLimitFile)) {
        file_put_contents($rateLimitFile, '');
    }
    
    $currentTime = time();
    $requests = [];
    
    if (filesize($rateLimitFile) > 0) {
        $lines = file($rateLimitFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $parts = explode('|', $line);
            if (count($parts) >= 2) {
                $requestIp = $parts[0];
                $requestTime = (int)$parts[1];
                
                // Only keep requests within the time window
                if ($requestTime > ($currentTime - $timeWindow)) {
                    $requests[] = $line;
                }
            }
        }
    }
    
    // Count requests from this IP
    $ipRequests = array_filter($requests, function($line) use ($ip) {
        return strpos($line, $ip . '|') === 0;
    });
    
    if (count($ipRequests) >= $maxRequests) {
        return false;
    }
    
    // Add current request
    $requests[] = $ip . '|' . $currentTime;
    
    // Write back to file
    file_put_contents($rateLimitFile, implode("\n", $requests) . "\n", LOCK_EX);
    
    return true;
}
?>

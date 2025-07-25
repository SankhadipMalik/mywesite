<?php
// TechToolsHub URL Shortener Redirect Handler
require_once 'config.php';

// Get the short code from the URL
$shortCode = $_GET['code'] ?? '';

// Validate short code
if (empty($shortCode)) {
    http_response_code(404);
    include '404.html';
    exit;
}

// Sanitize the short code
$shortCode = preg_replace('/[^a-zA-Z0-9_-]/', '', $shortCode);

if (empty($shortCode)) {
    http_response_code(404);
    include '404.html';
    exit;
}

// File to read URLs from
$urlsFile = 'urls.txt';

// Check if file exists
if (!file_exists($urlsFile)) {
    http_response_code(404);
    include '404.html';
    exit;
}

// Read and parse the URLs file
$found = false;
$originalUrl = '';

if (filesize($urlsFile) > 0) {
    $lines = file($urlsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        $parts = explode('|', $line, 4);
        
        if (count($parts) >= 2 && $parts[0] === $shortCode) {
            $originalUrl = $parts[1];
            $found = true;
            break;
        }
    }
}

if (!$found || empty($originalUrl)) {
    http_response_code(404);
    // Create a simple 404 page if not found
    ?>
    <!DOCTYPE html>
    <html lang="en" class="scroll-smooth">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Link Not Found - TechToolsHub</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            primary: '#3B82F6',
                            secondary: '#1E40AF'
                        }
                    }
                }
            }
        </script>
    </head>
    <body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen flex items-center justify-center">
        <div class="text-center max-w-md mx-auto px-4">
            <div class="mb-8">
                <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 20a7.962 7.962 0 01-5.002-1.709L5 20l1.709-1.998A7.962 7.962 0 014 12a8 8 0 018-8c4.418 0 8 3.582 8 8a7.962 7.962 0 01-1.709 5.002L20 19l-1.998-1.709z"></path>
                </svg>
                <h1 class="text-4xl font-bold mb-4">404 - Link Not Found</h1>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    The short link you're looking for doesn't exist or has been removed.
                </p>
            </div>
            
            <div class="space-y-4">
                <a href="/" class="inline-block bg-primary text-white px-6 py-3 rounded-lg hover:bg-secondary transition-colors font-semibold">
                    Go to Homepage
                </a>
                <br>
                <a href="/tools/url-shortener.html" class="inline-block border border-primary text-primary px-6 py-3 rounded-lg hover:bg-primary hover:text-white transition-colors font-semibold">
                    Create New Short Link
                </a>
            </div>
            
            <div class="mt-8 text-sm text-gray-500 dark:text-gray-400">
                <p>Short code: <code class="bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded"><?php echo htmlspecialchars($shortCode); ?></code></p>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Validate the URL format
if (!filter_var($originalUrl, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    echo "Invalid URL stored";
    exit;
}

// Log the redirect for analytics (optional)
$logEntry = date('Y-m-d H:i:s') . '|' . $shortCode . '|' . $originalUrl . '|' . $_SERVER['REMOTE_ADDR'] . '|' . ($_SERVER['HTTP_USER_AGENT'] ?? '') . "\n";
file_put_contents('redirect_logs.txt', $logEntry, FILE_APPEND | LOCK_EX);

// Security check: prevent redirects to dangerous schemes
$allowedSchemes = ['http', 'https'];
$urlScheme = parse_url($originalUrl, PHP_URL_SCHEME);

if (!in_array(strtolower($urlScheme), $allowedSchemes)) {
    http_response_code(400);
    echo "Unsafe redirect attempted";
    exit;
}

// Add security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Perform the redirect
header('Location: ' . $originalUrl, true, 302);

// Optional: Show a redirect page instead of immediate redirect
// Uncomment the following code if you want to show a redirect confirmation page

/*
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting... - TechToolsHub</title>
    <meta http-equiv="refresh" content="3;url=<?php echo htmlspecialchars($originalUrl); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#1E40AF'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center max-w-md mx-auto px-4">
        <div class="mb-8">
            <svg class="w-16 h-16 mx-auto text-primary mb-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <h1 class="text-2xl font-bold mb-4">Redirecting...</h1>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                You will be redirected to your destination in 3 seconds.
            </p>
        </div>
        
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-lg mb-6">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Destination:</p>
            <p class="text-primary break-all font-mono text-sm"><?php echo htmlspecialchars($originalUrl); ?></p>
        </div>
        
        <a href="<?php echo htmlspecialchars($originalUrl); ?>" class="inline-block bg-primary text-white px-6 py-3 rounded-lg hover:bg-secondary transition-colors font-semibold">
            Continue Now
        </a>
    </div>
    
    <script>
        // Redirect after 3 seconds
        setTimeout(function() {
            window.location.href = <?php echo json_encode($originalUrl); ?>;
        }, 3000);
    </script>
</body>
</html>
<?php
*/

exit;
?>

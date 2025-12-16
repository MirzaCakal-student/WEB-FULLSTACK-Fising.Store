
<?php
 //finished
// index.php

require 'vendor/autoload.php';
require_once __DIR__ . '/rest/config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CORS Headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authentication, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit();
}

// ===================================================
// 1. REQUIRE ROLES CLASS (MUST BE FIRST!)
// ===================================================
require_once __DIR__ . '/rest/Roles.php';

// ===================================================
// 2. REQUIRE MIDDLEWARE
// ===================================================
require_once __DIR__ . '/rest/MIDDLEWARE/AuthMiddleware.php';

// ===================================================
// 3. REQUIRE SERVICES
// ===================================================
require_once __DIR__ . '/rest/SERVICES/AuthService.php';
require_once __DIR__ . '/rest/SERVICES/AddressService.php';
require_once __DIR__ . '/rest/SERVICES/CartItemService.php';
require_once __DIR__ . '/rest/SERVICES/InventoryService.php';
require_once __DIR__ . '/rest/SERVICES/OrderService.php';
require_once __DIR__ . '/rest/SERVICES/OrderItemService.php';
require_once __DIR__ . '/rest/SERVICES/PaymentService.php';
require_once __DIR__ . '/rest/SERVICES/ProductService.php';
require_once __DIR__ . '/rest/SERVICES/UserService.php';
require_once __DIR__ . '/rest/SERVICES/WishlistItemService.php';

// ===================================================
// 4. REGISTER SERVICES
// ===================================================
Flight::register('auth_service', 'AuthService');
Flight::register('authMiddleware', 'AuthMiddleware');
Flight::register('addressService', 'AddressService');
Flight::register('cartItemService', 'CartItemService');
Flight::register('inventoryService', 'InventoryService');
Flight::register('orderService', 'OrderService');
Flight::register('orderItemService', 'OrderItemService');
Flight::register('paymentService', 'PaymentService');
Flight::register('productService', 'ProductService');
Flight::register('userService', 'UserService');
Flight::register('wishlistItemService', 'WishlistItemService');

// ===================================================
// 5. DEBUG ROUTE (Remove after testing)
// ===================================================
Flight::route('GET /debug/headers', function() {
    $debug = [
        'apache_request_headers' => function_exists('apache_request_headers') ? apache_request_headers() : 'not available',
        'getallheaders' => function_exists('getallheaders') ? getallheaders() : 'not available',
        'HTTP_AUTHORIZATION' => isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : 'not set',
        'REDIRECT_HTTP_AUTHORIZATION' => isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) ? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] : 'not set',
        'HTTP_AUTHENTICATION' => isset($_SERVER['HTTP_AUTHENTICATION']) ? $_SERVER['HTTP_AUTHENTICATION'] : 'not set',
        'all_server_keys' => array_keys($_SERVER)
    ];
    Flight::json($debug);
});

// ===================================================
// 6. MIDDLEWARE (Global Auth Check)
// ===================================================
Flight::before('start', function() {
    $url = Flight::request()->url;
    $method = Flight::request()->method;

    // PUBLIC ROUTES (No authentication required)
    $publicRoutes = [
        '/auth/login',
        '/auth/register',
        '/docs',
        '/debug/headers'
    ];

    // Check if URL starts with any public route
    foreach ($publicRoutes as $route) {
        if (strpos($url, $route) === 0) {
            return TRUE;
        }
    }

    // GET /products is public (browse products)
    if ($method === 'GET' && strpos($url, '/products') === 0) {
        return TRUE;
    }

    // PROTECTED ROUTES (Authentication required)
    try {
        // Try to get token from Authorization or Authentication header
        $token = null;

        // Method 1: Check $_SERVER for HTTP_AUTHORIZATION (standard)
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $token = $_SERVER['HTTP_AUTHORIZATION'];
        }
        // Method 1b: Check REDIRECT_HTTP_AUTHORIZATION (Apache mod_rewrite)
        elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $token = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }
        // Method 2: Check $_SERVER for HTTP_AUTHENTICATION (custom)
        elseif (isset($_SERVER['HTTP_AUTHENTICATION'])) {
            $token = $_SERVER['HTTP_AUTHENTICATION'];
        }
        // Method 2b: Check REDIRECT_HTTP_AUTHENTICATION (Apache mod_rewrite)
        elseif (isset($_SERVER['REDIRECT_HTTP_AUTHENTICATION'])) {
            $token = $_SERVER['REDIRECT_HTTP_AUTHENTICATION'];
        }
        // Method 3: Check apache_request_headers if available
        elseif (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if (isset($headers['Authorization'])) {
                $token = $headers['Authorization'];
            } elseif (isset($headers['authorization'])) {
                $token = $headers['authorization'];
            } elseif (isset($headers['Authentication'])) {
                $token = $headers['Authentication'];
            } elseif (isset($headers['authentication'])) {
                $token = $headers['authentication'];
            }
        }
        // Method 4: Check getallheaders if available
        elseif (function_exists('getallheaders')) {
            $headers = getallheaders();
            if (isset($headers['Authorization'])) {
                $token = $headers['Authorization'];
            } elseif (isset($headers['authorization'])) {
                $token = $headers['authorization'];
            } elseif (isset($headers['Authentication'])) {
                $token = $headers['Authentication'];
            } elseif (isset($headers['authentication'])) {
                $token = $headers['authentication'];
            }
        }
        // Method 5: Manual header parsing from $_SERVER
        else {
            foreach ($_SERVER as $key => $value) {
                $lower_key = strtolower($key);
                if ($lower_key === 'http_authorization' || $lower_key === 'http_authentication') {
                    $token = $value;
                    break;
                }
            }
        }

        if (!$token) {
            // Debug: Log what headers we actually received
            error_log("AUTH DEBUG - No token found. Available headers:");
            if (function_exists('apache_request_headers')) {
                error_log("apache_request_headers: " . json_encode(apache_request_headers()));
            }
            error_log("HTTP_AUTHORIZATION in \$_SERVER: " . (isset($_SERVER['HTTP_AUTHORIZATION']) ? "YES" : "NO"));
            error_log("REDIRECT_HTTP_AUTHORIZATION in \$_SERVER: " . (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) ? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] : "NO"));

            Flight::halt(401, json_encode([
                "success" => false,
                "message" => "Missing authentication token"
            ]));
        }

        // Remove "Bearer " prefix if present
        if (stripos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $decoded_token = JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));

        // Store user info for routes to use
        Flight::set('user', $decoded_token->user);
        Flight::set('jwt_token', $token);

        // Debug logging
        error_log("AUTH SUCCESS - User set: " . json_encode($decoded_token->user));

        return TRUE;

    } catch (\Exception $e) {
        Flight::halt(401, json_encode([
            "success" => false,
            "message" => "Invalid or expired token: " . $e->getMessage()
        ]));
    }
});

// ===================================================
// 6. LOAD ROUTES
// ===================================================
require_once __DIR__ . '/rest/ROUTES/AuthRoutes.php';
require_once __DIR__ . '/rest/ROUTES/AddressRoutes.php';
require_once __DIR__ . '/rest/ROUTES/CartItemRoutes.php';
require_once __DIR__ . '/rest/ROUTES/CheckoutRoutes.php';
require_once __DIR__ . '/rest/ROUTES/InventoryRoutes.php';
require_once __DIR__ . '/rest/ROUTES/OrderRoutes.php';
require_once __DIR__ . '/rest/ROUTES/OrderItemRoutes.php';
require_once __DIR__ . '/rest/ROUTES/PaymentRoutes.php';
require_once __DIR__ . '/rest/ROUTES/ProductRoutes.php';
require_once __DIR__ . '/rest/ROUTES/UserRoutes.php';
require_once __DIR__ . '/rest/ROUTES/WishlistItemRoutes.php';
// ===================================================
// 7. START APPLICATION
// ===================================================
Flight::start();
?>
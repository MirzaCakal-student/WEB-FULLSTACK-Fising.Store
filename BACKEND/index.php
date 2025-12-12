<?php
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

// 1. REGISTER SERVICES
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

// Register services (Ensure names match what you use in Routes)
Flight::register('auth_service', 'AuthService'); // Matching Lab 07 naming
Flight::register('addressService', 'AddressService');
Flight::register('cartItemService', 'CartItemService');
Flight::register('inventoryService', 'InventoryService');
Flight::register('orderService', 'OrderService');
Flight::register('orderItemService', 'OrderItemService');
Flight::register('paymentService', 'PaymentService');
Flight::register('productService', 'ProductService');
Flight::register('userService', 'UserService');
Flight::register('wishlistItemService', 'WishlistItemService');

// 2. MIDDLEWARE (The Robust Fix)
// Intercept all requests to check for tokens
Flight::route('/*', function() {
    $url = Flight::request()->url;

    // PUBLIC ROUTES CHECK
    // We check if the URL *contains* these strings using !== false
    // This allows /index.php/auth/login OR /auth/login to both work.
    if (
        strpos($url, '/auth/login') !== false ||
        strpos($url, '/auth/register') !== false ||
        strpos($url, '/products') !== false ||
        strpos($url, '/docs') !== false
    ) {
        return TRUE; // Allow access to the route
    }

    // PROTECTED ROUTES CHECK
    try {
        $token = Flight::request()->getHeader("Authentication");
        
        if(!$token) {
            Flight::halt(401, json_encode(["message" => "Missing authentication token"]));
        }

        $decoded_token = JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));

        Flight::set('user', $decoded_token->user);
        Flight::set('jwt_token', $token);
        return TRUE;
        
    } catch (\Exception $e) {
        Flight::halt(401, json_encode(["message" => "Invalid token: " . $e->getMessage()]));
    }
});

// 3. LOAD ROUTES
require_once __DIR__ . '/rest/ROUTES/AuthRoutes.php';
require_once __DIR__ . '/rest/ROUTES/AddressRoutes.php';
require_once __DIR__ . '/rest/ROUTES/CartItemRoutes.php';
require_once __DIR__ . '/rest/ROUTES/InventoryRoutes.php';
require_once __DIR__ . '/rest/ROUTES/OrderRoutes.php';
require_once __DIR__ . '/rest/ROUTES/OrderItemRoutes.php';
require_once __DIR__ . '/rest/ROUTES/PaymentRoutes.php';
require_once __DIR__ . '/rest/ROUTES/ProductRoutes.php';
require_once __DIR__ . '/rest/ROUTES/UserRoutes.php';
require_once __DIR__ . '/rest/ROUTES/WishlistItemRoutes.php';

Flight::start();
?>
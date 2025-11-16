<?php
// BACKEND/index.php

require __DIR__ . '/vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ====== SERVICES ======
require_once __DIR__ . '/rest/SERVICES/BaseService.php';

require_once __DIR__ . '/rest/SERVICES/AddressService.php';
require_once __DIR__ . '/rest/SERVICES/CartItemService.php';
require_once __DIR__ . '/rest/SERVICES/InventoryService.php';
require_once __DIR__ . '/rest/SERVICES/OrderItemService.php';
require_once __DIR__ . '/rest/SERVICES/OrderService.php';
require_once __DIR__ . '/rest/SERVICES/PaymentService.php';
require_once __DIR__ . '/rest/SERVICES/ProductService.php';
require_once __DIR__ . '/rest/SERVICES/UserService.php';
require_once __DIR__ . '/rest/SERVICES/WishlistItemService.php';

// ====== REGISTER SERVICES IN FLIGHT ======
Flight::register('addressService',      'AddressService');
Flight::register('cartItemService',     'CartItemService');
Flight::register('inventoryService',    'InventoryService');
Flight::register('orderItemService',    'OrderItemService');
Flight::register('orderService',        'OrderService');
Flight::register('paymentService',      'PaymentService');
Flight::register('productService',      'ProductService');
Flight::register('userService',         'UserService');
Flight::register('wishlistItemService', 'WishlistItemService');

// ====== ROUTES ======
require_once __DIR__ . '/rest/ROUTES/AddressRoutes.php';
require_once __DIR__ . '/rest/ROUTES/CartItemRoutes.php';
require_once __DIR__ . '/rest/ROUTES/CartTotalsRoutes.php';
require_once __DIR__ . '/rest/ROUTES/InventoryRoutes.php';
require_once __DIR__ . '/rest/ROUTES/OrderItemRoutes.php';
require_once __DIR__ . '/rest/ROUTES/OrderRoutes.php';
require_once __DIR__ . '/rest/ROUTES/PaymentRoutes.php';
require_once __DIR__ . '/rest/ROUTES/ProductRoutes.php';
require_once __DIR__ . '/rest/ROUTES/UserRoutes.php';
require_once __DIR__ . '/rest/ROUTES/WishlistItemRoutes.php';

// ====== START FLIGHT ======
Flight::start();



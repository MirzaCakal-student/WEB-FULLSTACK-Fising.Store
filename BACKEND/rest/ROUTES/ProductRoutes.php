<?php

/**
 * GET all products (PUBLIC - no auth required)
 */
Flight::route('GET /products', function() {
    try {
        $products = Flight::productService()->get_all();
        Flight::json(['success' => true, 'data' => $products]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * GET product by ID (PUBLIC)
 */
Flight::route('GET /products/@id', function($id) {
    try {
        $product = Flight::productService()->get_by_id($id);
        Flight::json(['success' => true, 'data' => $product]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * CREATE new product (ADMIN ONLY)
 */
Flight::route('POST /products', function() {
    // Check if user is admin
    Flight::authMiddleware()->authorizeRole(Roles::ADMIN);
    
    $data = Flight::request()->data->getData();
    
    try {
        $product_id = Flight::productService()->add($data);
        Flight::json(['success' => true, 'data' => ['product_id' => $product_id]]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

/**
 * UPDATE product (ADMIN ONLY)
 */
Flight::route('PUT /products/@id', function($id) {
    Flight::authMiddleware()->authorizeRole(Roles::ADMIN);
    
    $data = Flight::request()->data->getData();
    
    try {
        $result = Flight::productService()->update($id, $data);
        Flight::json(['success' => true, 'data' => $result]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

/**
 * DELETE product (ADMIN ONLY)
 */
Flight::route('DELETE /products/@id', function($id) {
    Flight::authMiddleware()->authorizeRole(Roles::ADMIN);
    
    try {
        $result = Flight::productService()->delete($id);
        Flight::json(['success' => true, 'data' => $result]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

?>
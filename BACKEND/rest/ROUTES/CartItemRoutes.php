<?php

/**
 * GET cart items for current user
 */
Flight::route('GET /cart', function() {
    $currentUser = Flight::get('user');
    
    try {
        $items = Flight::cartItemService()->get_cart_by_user($currentUser->user_id);
        Flight::json(['success' => true, 'data' => $items]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * ADD item to cart
 */
Flight::route('POST /cart', function() {
    $currentUser = Flight::get('user');
    $data = Flight::request()->data->getData();
    
    // Force the user_id to be the current user
    $data['user_id'] = $currentUser->user_id;
    
    try {
        $cart_item_id = Flight::cartItemService()->add($data);
        Flight::json(['success' => true, 'data' => ['cart_item_id' => $cart_item_id]]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

/**
 * UPDATE cart item quantity
 */
Flight::route('PUT /cart/@id', function($id) {
    $currentUser = Flight::get('user');
    $data = Flight::request()->data->getData();
    
    // Verify ownership (check if this cart item belongs to current user)
    $cartItem = Flight::cartItemService()->get_by_id($id);
    if (!$cartItem || $cartItem['user_id'] != $currentUser->user_id) {
        Flight::halt(403, json_encode(['message' => 'Access denied']));
    }
    
    try {
        $result = Flight::cartItemService()->update($id, $data);
        Flight::json(['success' => true, 'data' => $result]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

/**
 * DELETE cart item
 */
Flight::route('DELETE /cart/@id', function($id) {
    $currentUser = Flight::get('user');
    
    // Verify ownership
    $cartItem = Flight::cartItemService()->get_by_id($id);
    if (!$cartItem || $cartItem['user_id'] != $currentUser->user_id) {
        Flight::halt(403, json_encode(['message' => 'Access denied']));
    }
    
    try {
        $result = Flight::cartItemService()->delete($id);
        Flight::json(['success' => true, 'data' => $result]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

/**
 * CLEAR entire cart for current user
 */
Flight::route('DELETE /cart', function() {
    $currentUser = Flight::get('user');
    
    try {
        $result = Flight::cartItemService()->clear_cart($currentUser->user_id);
        Flight::json(['success' => true, 'data' => $result]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

?>
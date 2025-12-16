<?php
 //finished
/**
 * GET wishlist items for current user
 */
Flight::route('GET /wishlist-items', function() {
    $currentUser = Flight::get('user');
    
    if (!$currentUser) {
        Flight::json(['success' => false, 'message' => 'User not authenticated'], 401);
        return;
    }
    
    try {
        $items = Flight::wishlistItemService()->get_wishlist_by_user($currentUser->user_id);
        Flight::json(['success' => true, 'data' => $items]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * ADD item to wishlist (or REMOVE if already exists - toggle)
 */
Flight::route('POST /wishlist-items', function() {
    $currentUser = Flight::get('user');
    
    if (!$currentUser) {
        Flight::json(['success' => false, 'message' => 'User not authenticated'], 401);
        return;
    }
    
    $data = Flight::request()->data->getData();
    
    // Force user_id to be current user
    $data['user_id'] = $currentUser->user_id;
    
    try {
        $created = Flight::wishlistItemService()->toggle_wishlist($data);
        Flight::json(['success' => true, 'data' => $created]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

/**
 * DELETE wishlist item
 */
Flight::route('DELETE /wishlist-items/@id', function($id) {
    $currentUser = Flight::get('user');
    
    if (!$currentUser) {
        Flight::json(['success' => false, 'message' => 'User not authenticated'], 401);
        return;
    }
    
    // Verify ownership
    $wishlistItem = Flight::wishlistItemService()->get_by_id($id);
    if (!$wishlistItem || $wishlistItem['user_id'] != $currentUser->user_id) {
        Flight::halt(403, json_encode(['message' => 'Access denied']));
    }
    
    try {
        $res = Flight::wishlistItemService()->delete_wishlist_item($id);
        Flight::json(['success' => true, 'data' => $res]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});
?>
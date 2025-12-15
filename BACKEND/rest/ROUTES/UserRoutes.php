<?php

/**
 * GET all users (ADMIN ONLY)
 */
Flight::route('GET /users', function () {
    Flight::authMiddleware()->authorizeRole(Roles::ADMIN);
    
    try {
        $users = Flight::userService()->get_users();
        Flight::json(['success' => true, 'data' => $users]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * GET user by ID (User can view their own profile, Admin can view any)
 */
Flight::route('GET /users/@id', function ($id) {
    $currentUser = Flight::get('user');

    if (!$currentUser) {
        Flight::halt(401, json_encode(['success' => false, 'message' => 'User not authenticated']));
    }

    // Admin can see anyone, User can only see themselves
    if (isset($currentUser->role) && $currentUser->role !== Roles::ADMIN && isset($currentUser->user_id) && $currentUser->user_id != $id) {
        Flight::halt(403, json_encode(['success' => false, 'message' => 'Access denied']));
    }

    try {
        $user = Flight::userService()->get_user_by_id($id);
        Flight::json(['success' => true, 'data' => $user]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

/**
 * CREATE new user (PUBLIC for registration, handled in AuthRoutes)
 * This route is for ADMIN to manually create users
 */
Flight::route('POST /users', function () {
    Flight::authMiddleware()->authorizeRole(Roles::ADMIN);
    
    $data = Flight::request()->data->getData();
    try {
        $created = Flight::userService()->add_user($data);
        Flight::json(['success' => true, 'data' => $created]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

/**
 * UPDATE user (User can update themselves, Admin can update anyone)
 */
Flight::route('PUT /users/@id', function ($id) {
    $currentUser = Flight::get('user');

    if (!$currentUser) {
        Flight::halt(401, json_encode(['success' => false, 'message' => 'User not authenticated']));
    }

    // Admin can update anyone, User can only update themselves
    if (isset($currentUser->role) && $currentUser->role !== Roles::ADMIN && isset($currentUser->user_id) && $currentUser->user_id != $id) {
        Flight::halt(403, json_encode(['success' => false, 'message' => 'Access denied']));
    }

    $data = Flight::request()->data->getData();
    try {
        $res = Flight::userService()->update_user($id, $data);
        Flight::json(['success' => true, 'data' => $res]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

/**
 * DELETE user (User can delete themselves, Admin can delete anyone)
 */
Flight::route('DELETE /users/@id', function ($id) {
    $currentUser = Flight::get('user');

    if (!$currentUser) {
        Flight::halt(401, json_encode(['success' => false, 'message' => 'User not authenticated']));
    }

    // Admin can delete anyone, User can only delete themselves
    if (isset($currentUser->role) && $currentUser->role !== Roles::ADMIN && isset($currentUser->user_id) && $currentUser->user_id != $id) {
        Flight::halt(403, json_encode(['success' => false, 'message' => 'Access denied']));
    }

    try {
        $res = Flight::userService()->delete_user($id);
        Flight::json(['success' => true, 'data' => $res]);
    } catch (Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});
?>
<?php
 //finished
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {

    /**
     * Verify JWT Token
     */
    public function verifyToken() {
        // 1. Get token from header using FlightPHP method
        $token = Flight::request()->getHeader('Authentication');

        if (!$token) {
            Flight::halt(401, json_encode(["message" => "Missing authentication token"]));
        }

        try {
            // 2. Decode JWT
            $decoded = JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));
            
            // 3. Store user info in Flight for later use
            Flight::set('user', $decoded->user);
            Flight::set('jwt_token', $token);
            
            return TRUE;
        } catch (\Exception $e) {
            Flight::halt(401, json_encode(["message" => "Invalid or expired token: " . $e->getMessage()]));
        }
    }

    /**
     * Check if user has a specific role
     */
    public function authorizeRole($requiredRole) {
        $user = Flight::get('user');
        
        if (!$user) {
            Flight::halt(401, json_encode(["message" => "User not authenticated"]));
        }

        if ($user->role !== $requiredRole) {
            Flight::halt(403, json_encode(["message" => "Access denied: insufficient privileges"]));
        }

        return TRUE;
    }

    /**
     * Check if user has one of multiple roles
     */
    public function authorizeRoles($roles) {
        $user = Flight::get('user');
        
        if (!$user) {
            Flight::halt(401, json_encode(["message" => "User not authenticated"]));
        }

        if (!in_array($user->role, $roles)) {
            Flight::halt(403, json_encode(["message" => "Forbidden: role not allowed"]));
        }

        return TRUE;
    }

    /**
     * Check if user owns the resource (e.g., their own cart)
     */
    public function authorizeOwner($resourceUserId) {
        $user = Flight::get('user');
        
        if (!$user) {
            Flight::halt(401, json_encode(["message" => "User not authenticated"]));
        }

        // Admin can access everything
        if ($user->role === 'admin') {
            return TRUE;
        }

        // User can only access their own resources
        if ($user->user_id != $resourceUserId) {
            Flight::halt(403, json_encode(["message" => "Access denied: you can only access your own resources"]));
        }

        return TRUE;
    }
}
?>
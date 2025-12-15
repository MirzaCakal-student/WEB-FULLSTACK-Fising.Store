<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../DAO/AuthDAO.php';
require_once __DIR__ . '/../config.php';

use Firebase\JWT\JWT;

class AuthService extends BaseService {
    private $auth_dao;

    public function __construct() {
        $this->auth_dao = new AuthDAO();
        parent::__construct(new AuthDAO());
    }

    public function register($entity) {
        // 1. Validate
        if (empty($entity['email']) || empty($entity['password'])) {
            return ['success' => false, 'error' => 'Email and password are required.'];
        }

        // 2. Check existence
        $user = $this->auth_dao->get_user_by_email($entity['email']);
        if ($user) {
            return ['success' => false, 'error' => 'Email already registered.'];
        }

        // 3. Hash Password
        $entity['password_hash'] = password_hash($entity['password'], PASSWORD_BCRYPT);
        unset($entity['password']);
        unset($entity['confirm_password']);

        // 4. Default Role
        if (!isset($entity['role'])) {
            $entity['role'] = 'user';
        }

        try {
            // 5. Insert and Get ID
            $user_id = $this->auth_dao->insert($entity); 
            
            // 6. AUTO-LOGIN LOGIC (FETCH USER & GENERATE TOKEN)
            // We need to fetch the full user object to put inside the JWT
            $newUser = $this->auth_dao->get_user_by_email($entity['email']);
            unset($newUser['password_hash']); // Security

            $payload = [
                'user' => $newUser,
                'iat' => time(),
                'exp' => time() + (60 * 60 * 48) // 48 hours
            ];

            $token = JWT::encode($payload, Config::JWT_SECRET(), 'HS256');

            // Return User AND Token
            return [
                'success' => true, 
                'data' => [
                    'user' => $newUser,
                    'token' => $token
                ]
            ];

        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Registration error: ' . $e->getMessage()];
        }
    }

    public function login($entity) {
        // 1. Get user
        $user = $this->auth_dao->get_user_by_email($entity['email']);

        // 2. Verify password
        // We check against 'password_hash' because that's what is in the DB
        if (!$user || !password_verify($entity['password'], $user['password_hash'])) {
            return ['success' => false, 'error' => 'Invalid credentials'];
        }

        // 3. Remove sensitive data
        unset($user['password_hash']);

        // 4. Generate Token
        $payload = [
            'user' => $user,
            'iat' => time(),
            'exp' => time() + (60 * 60 * 48) // 48 hours
        ];

        $token = JWT::encode($payload, Config::JWT_SECRET(), 'HS256');

        return [
            'success' => true, 
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ];
    }
}
?>
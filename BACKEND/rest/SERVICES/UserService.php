<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../DAO/UserDAO.php';

class UserService extends BaseService {

    public function __construct() {
        parent::__construct(new UserDAO());
    }

    public function get_users() {
        return $this->dao->getAll();
    }

    public function get_user_by_id($id) {
        $user = $this->dao->getById($id);
        if ($user) unset($user['password_hash']);
        return $user;
    }

    public function get_user_by_email($email) {
        $user = $this->dao->getByEmail($email);
        if ($user) unset($user['password_hash']);
        return $user;
    }

    public function add_user($data) {
        if (empty($data['username'])) {
            throw new Exception("Username is required.");
        }
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Valid email is required.");
        }
        if (empty($data['password'])) {
            throw new Exception("Password is required.");
        }
        if (strlen($data['password']) < 6) {
            throw new Exception("Password must be at least 6 characters.");
        }

        // hash password
        $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
        unset($data['password']);

        if (empty($data['role'])) {
            $data['role'] = 'customer';
        }

        return $this->dao->insert($data);
    }

    public function update_user($id, $data) {
        if (isset($data['email']) &&
            !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email is not valid.");
        }

        if (!empty($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
            unset($data['password']);
        }

        return $this->dao->update($id, $data);
    }

    public function delete_user($id) {
        return $this->dao->delete($id);
    }
}

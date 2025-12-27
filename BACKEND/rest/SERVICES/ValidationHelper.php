<?php

/**
 * ValidationHelper - Input validation and sanitization for security
 */
class ValidationHelper {

    /**
     * Sanitize string input to prevent XSS
     */
    public static function sanitizeString($input) {
        if (empty($input)) return '';

        // Remove any HTML tags and trim
        $sanitized = strip_tags(trim($input));

        // Convert special characters to HTML entities
        $sanitized = htmlspecialchars($sanitized, ENT_QUOTES, 'UTF-8');

        return $sanitized;
    }

    /**
     * Sanitize email input
     */
    public static function sanitizeEmail($email) {
        if (empty($email)) return '';

        // Remove all illegal characters from email
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);

        return strtolower($email);
    }

    /**
     * Validate email format
     */
    public static function validateEmail($email) {
        if (empty($email)) {
            throw new Exception("Email is required.");
        }

        $email = self::sanitizeEmail($email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }

        return $email;
    }

    /**
     * Validate and sanitize username
     */
    public static function validateUsername($username, $minLength = 3) {
        if (empty($username)) {
            throw new Exception("Username is required.");
        }

        $username = self::sanitizeString($username);

        if (strlen($username) < $minLength) {
            throw new Exception("Username must be at least {$minLength} characters.");
        }

        // Allow only letters and spaces
        if (!preg_match('/^[a-zA-Z\s]+$/', $username)) {
            throw new Exception("Username should contain only letters and spaces.");
        }

        return $username;
    }

    /**
     * Validate password strength
     */
    public static function validatePassword($password, $minLength = 6) {
        if (empty($password)) {
            throw new Exception("Password is required.");
        }

        if (strlen($password) < $minLength) {
            throw new Exception("Password must be at least {$minLength} characters.");
        }

        // Check password complexity
        if (!preg_match('/[A-Z]/', $password)) {
            throw new Exception("Password must contain at least one uppercase letter.");
        }

        if (!preg_match('/[a-z]/', $password)) {
            throw new Exception("Password must contain at least one lowercase letter.");
        }

        if (!preg_match('/[0-9]/', $password)) {
            throw new Exception("Password must contain at least one number.");
        }

        return $password;
    }

    /**
     * Validate and sanitize price
     */
    public static function validatePrice($price) {
        if (!isset($price)) {
            throw new Exception("Price is required.");
        }

        $price = floatval($price);

        if ($price < 0) {
            throw new Exception("Price cannot be negative.");
        }

        if ($price > 999999) {
            throw new Exception("Price is too high.");
        }

        return $price;
    }

    /**
     * Validate and sanitize stock quantity
     */
    public static function validateStock($stock) {
        if (!isset($stock)) {
            return 0;
        }

        $stock = intval($stock);

        if ($stock < 0) {
            throw new Exception("Stock quantity cannot be negative.");
        }

        return $stock;
    }

    /**
     * Validate phone number
     */
    public static function validatePhone($phone) {
        if (empty($phone)) {
            throw new Exception("Phone number is required.");
        }

        $phone = trim($phone);

        // Allow only numbers, spaces, plus, minus, and parentheses
        if (!preg_match('/^[\d\s\+\-\(\)]+$/', $phone)) {
            throw new Exception("Invalid phone number format.");
        }

        // Check minimum length (at least 8 digits)
        $digitsOnly = preg_replace('/\D/', '', $phone);
        if (strlen($digitsOnly) < 8) {
            throw new Exception("Phone number must have at least 8 digits.");
        }

        return $phone;
    }

    /**
     * Sanitize and validate product name
     */
    public static function validateProductName($name) {
        if (empty($name)) {
            throw new Exception("Product name is required.");
        }

        $name = self::sanitizeString($name);

        if (strlen($name) < 3) {
            throw new Exception("Product name must be at least 3 characters.");
        }

        return $name;
    }

    /**
     * Validate required field
     */
    public static function validateRequired($value, $fieldName) {
        if (empty($value) && $value !== '0' && $value !== 0) {
            throw new Exception("{$fieldName} is required.");
        }

        return trim($value);
    }

    /**
     * Sanitize address input
     */
    public static function sanitizeAddress($address) {
        if (empty($address)) return '';

        $address = self::sanitizeString($address);

        if (strlen($address) < 5) {
            throw new Exception("Address must be at least 5 characters.");
        }

        return $address;
    }

    /**
     * Validate ZIP/Postal code
     */
    public static function validateZipCode($zip) {
        if (empty($zip)) {
            throw new Exception("ZIP/Postal code is required.");
        }

        $zip = trim(strtoupper($zip));

        if (!preg_match('/^[A-Z0-9\s\-]+$/', $zip) || strlen($zip) < 3) {
            throw new Exception("Invalid ZIP/Postal code format.");
        }

        return $zip;
    }
}
?>

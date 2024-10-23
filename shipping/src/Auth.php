<?php
require __DIR__ . '/../config/env.php';
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth {
    private static $secret;

    public static function init() {
        // Load the JWT secret key from the environment variable
        self::$secret = $_ENV['JWT_SECRET'];
        // $variabtest = 'hello';
        error_log('JWT_SECRET ' . self::$secret);
    }

    // Function to validate the JWT token and check for the valid role
    public static function validateToken($jwt) {
        try {
            // Decode the JWT token
            $decoded = JWT::decode($jwt, new Key(self::$secret, 'HS256'));
            error_log('decoded' . json_encode($decoded, JSON_PRETTY_PRINT));
            // Convert the decoded data into an array
            $userData = (array) $decoded;

            // Retrieve the valid role from environment variables
            $validRole = $_ENV['VALID_ROLE_SHIPPING'];  // e.g., "logisticsManager"

            // Check if the role in the JWT matches the valid role
            if (isset($userData['role']) && $userData['role'] === $validRole) {
                // Role is valid, return the decoded data
                error_log("token valid");
                return $userData;
            } else {
                // Role is invalid, throw an exception
                throw new Exception('Unauthorized: Invalid role.');
            }
        } catch (Exception $e) {
            // If token decoding or role validation fails, throw an exception
            throw new Exception('Unauthorized: Invalid token or role.');
        }
    }
}

<?php
namespace App\Controllers;

use App\Models\User;

class LoginController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new User($db);
    }

    public function showLoginForm() {
        include 'path/to/views/login-form.php'; // Adjust the path accordingly
    }

    public function login() {
        // Check if the request method is POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? ''; // Use null coalescing to avoid undefined index
            $password = $_POST['password'] ?? '';

            // Basic input validation
            if (empty($username) || empty($password)) {
                echo "Please enter both username and password.";
                return;
            }

            if ($this->userModel->login($username, $password)) {
                echo "Login successful!"; // Redirect to dashboard or homepage
                // Consider using header() for redirection instead of echo
            } else {
                echo "Login failed. Invalid username or password.";
            }
        } else {
            echo "Invalid request method.";
        }
    }
}

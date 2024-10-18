<?php 

namespace App\Controllers;

use App\Models\User;

class LoginController extends BaseController
{
    public function showForm() {
        // Show the login form without errors
        return $this->render('login-form');
    }

    public function login() {
        // Initialize an array to store validation errors
        $errors = [];
        
        // Start session
        session_start();

        // Initialize login attempts if not set
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
        }

        try {
            // Retrieve form data
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            // Required field check
            if (empty($username) || empty($password)) {
                $errors[] = "Both fields are required.";
            }

            // Check for errors before processing
            if (!empty($errors)) {
                return $this->render('login-form', ['errors' => $errors, 'disabled' => $_SESSION['login_attempts'] >= 3]);
            }

            // Validate credentials
            $user = new User();
            $hashedPassword = $user->getPassword($username); // Retrieve the hashed password

            if ($hashedPassword && password_verify($password, $hashedPassword)) {
                // Successful login
                $_SESSION['is_logged_in'] = true; // Set login state
                $_SESSION['user_id'] = $username; // Set user identifier
                $_SESSION['login_attempts'] = 0; // Reset login attempts

                // Redirect to welcome page
                header("Location: /welcome");
                exit();
            } else {
                $_SESSION['login_attempts']++;
                $errors[] = "Invalid username or password.";

                if ($_SESSION['login_attempts'] >= 3) {
                    $errors[] = "Too many failed login attempts. The form is now disabled.";
                }

                return $this->render('login-form', ['errors' => $errors, 'disabled' => $_SESSION['login_attempts'] >= 3]);
            }
        } catch (Exception $e) {
            $errors[] = "An unexpected error occurred: " . $e->getMessage();
            return $this->render('login-form', ['errors' => $errors, 'disabled' => $_SESSION['login_attempts'] >= 3]);
        }
    }

    public function welcome() {
        session_start();

        // Check if user is logged in
        if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
            header("Location: /login-form");
            exit();
        }

        // Fetch all users to display
        $userModel = new User();
        $users = $userModel->getAllUsers();

        // Render welcome page with users data
        return $this->render('welcome', ['users' => $users]);
    }

    public function logout() {
        session_start();

        // Destroy session variables and the session itself
        session_unset();
        session_destroy();

        // Redirect to the login form
        header("Location: /login-form");
        exit();
    }
}

